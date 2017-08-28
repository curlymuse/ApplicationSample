<?php

namespace Tests;

use App;
use Artisan;
use \Mockery;
use App\Models\Role;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Support\AssemblerDispatcher;
use Codeception\Lib\Generator\PhpUnit;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Bus\Dispatcher;
use SRLabs\Utilities\Traits\TestingDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use TestingDatabaseTrait;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://resbeat.localhost';

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \App\Models\Hotel
     */
    protected $hotel;

    /**
     * @var \App\Models\Licensee
     */
    protected $licensee;

    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase('staging', 'testing');

        $this->actingAsUser();
    }

    protected function tearDown()
    {
        parent::tearDown();

        // Cleaning up after ourselves for better memory utilization
        // via: https://stackoverflow.com/questions/13537545/clear-memory-being-used-by-php#13551745
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        if (App::environment() == 'acceptance') {
            Artisan::call('migrate:refresh');
        }

        return $app;
    }

    /**
     * @param null $user
     */
    public function actingAsUser($user = null)
    {

        // ID 1 is the default user in the test db
        $this->user = $user ?: User::find(1);

        $this->actingAs($this->user);
    }

    /**
     * @param null $licensee
     */
    public function actingAsLicensee($licensee = null)
    {
        $this->actingAsUser();

        $this->licensee = ($licensee) ?: Licensee::find(1);

        $this->user->roles()->attach(
            factory(Role::class)->create()->id,
            [
                'rolable_type' => Licensee::class,
                'rolable_id'   => $this->licensee->id,
            ]
        );
    }

    /**
     * @param null $hotel
     */
    public function actingAsHotel($hotel = null)
    {
        $this->hotel = ($hotel) ? $hotel : factory(Hotel::class)->create();

        $this->withSession(['hotel.id' => $this->hotel->id]);
    }

    /**
     * Expect a specific Exception class with a specific error message,
     * given the job and condition being tested
     *
     * @param $jobClass
     * @param $conditionClass
     * @param $exceptionClass
     */
    public function expectsJobPolicyException($jobClass, $conditionClass, $exceptionClass)
    {
        $expectedError = trans(
            sprintf(
                'policies/%sPolicy.%s',
                class_basename($jobClass),
                class_basename($conditionClass)
            )
        );
        $this->setExpectedException($exceptionClass, $expectedError);
    }

    /**
     * @param $policyClass
     *
     * @return \Mockery\MockInterface
     */
    public function expectsPolicy($policyClass)
    {
        $mock = Mockery::mock($policyClass);
        $mock->shouldReceive('complies')
            ->once()
            ->andReturn(true);
        $this->instance($policyClass, $mock);

        return $mock;
    }

    /**
     * Assert response is JSON string for the supplied object
     *
     * @param $object
     */
    protected function assertJsonObject($object)
    {
        return $this->assertJson(json_encode($object));
    }

    /**
     * Assert response is NOT 200
     *
     * @return $this
     */
    protected function assertResponseNotOk()
    {
        $this->assertNotEquals(200, $this->response->getStatusCode(), "Received incorrect 200 response.");

        return $this;
    }

    /**
     * Mocks the handle method of the assembler, which is called
     * during $this->assemble(), as well as other common methods evoked
     * directly by the controller after the Assembler is returned from dispatch.
     * Optionally, data can be supplied for output
     *
     * @param       $assemblerClass
     * @param array $output
     */
    protected function expectsAssembler($assemblerClass, $output = [])
    {
        //  Mock the Assembler itself
        $assemblerMock = Mockery::mock($assemblerClass);
        $assemblerMock->shouldReceive('only')
            ->andReturnUsing(
                function ($keys) use ($output) {
                    $keys = (is_array($keys)) ? $keys : func_get_args();

                    return collect($output)->only($keys)->toArray();
                }
            );
        $this->instance($assemblerClass, $assemblerMock);

        //  Mock the assembler dispatcher so it returns the Assembler
        $dispatcherMock = Mockery::mock(AssemblerDispatcher::class);
        $dispatcherMock->shouldReceive('assemble')
            ->once()
            ->with($assemblerClass)
            ->andReturn($assemblerMock);
        $this->instance(AssemblerDispatcher::class, $dispatcherMock);
    }

    /**
     * @param mixed $mockClass
     *
     * @return mixed
     */
    protected function mock($mockClass)
    {
        $mock = Mockery::mock($mockClass);
        $this->instance($mockClass, $mock);

        return $mock;
    }

    /**
     * @param mixed $repository
     *
     * @return \App\Repositories\Eloquent\Repository
     */
    protected function expectsRepository($repository)
    {
        $mock = Mockery::mock($repository);
        $mock->shouldReceive('find')->byDefault();
        $mock->shouldReceive('store')->byDefault();
        $mock->shouldReceive('exists')->byDefault()->andReturn(true);
        $mock->shouldReceive('all')->andReturn([])->byDefault();

        $this->instance($repository, $mock);

        return $mock;
    }

    /**
     * Mocks a form request with all of the standard
     * methods called when it is instantiated be the IoC
     * container on a controller method call
     * Optionally, pass in an array of input data, to be returned
     * with the request object's mock
     *
     * @param \Illuminate\Foundation\Http\FormRequest $formRequest
     * @param array                                   $data (optional)
     * @param bool                                    $hasFile
     *
     * @return \Mockery\Mock
     */
    protected function expectsFormRequest($formRequest, $data = [], $hasFile = false)
    {
        $mock = Mockery::mock($formRequest)->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('passesAuthorization')->andReturn(true);
        $mock->shouldReceive('validate');
        $mock->shouldReceive('offsetExists');
        $mock->shouldReceive('file')
            ->andReturnUsing(
                function($key) use ($data) {
                    if (array_has($data, $key)) {
                        $mock = $this->mock(UploadedFile::class);
                        $mock->shouldReceive('getClientOriginalName');
                        $mock->shouldReceive('hashName')
                            ->andReturn(str_random());
                        $mock->shouldReceive('storePublicly');
                        return (is_array(array_get($data, $key))) ? [$mock] : $mock;
                    }
                }
            );
        $mock->shouldReceive('get')
            ->andReturnUsing(
                function ($key) use ($data) {
                    return array_get($data, $key);
                }
            );
        $mock->shouldReceive('has')
            ->andReturnUsing(
                function ($key) use ($data) {
                    return array_has($data, $key);
                }
            );
        $mock->shouldReceive('only')
            ->andReturnUsing(
                function ($keys) use ($data) {
                    $keys = (is_array($keys)) ? $keys : [$keys];
                    return static::array_selection($data, $keys);
                }
            );
        $mock->shouldReceive('all')->andReturn($data);
        $mock->shouldReceive('input')->andReturn([]);
        $mock->shouldReceive('hasFile')->andReturn($hasFile);
        $mock->shouldReceive('ajax')->andReturn(true);

        $this->instance($formRequest, $mock);

        return $mock;
    }

    /**
     * Expects mail to be sent using this mailer
     *
     * @param string $mailerClass
     * @param array $recipients
     */
    public function expectsMailer($mailerClass, $recipients = [])
    {
        \Mail::shouldReceive('to->queue')
            ->once()
            ->with($mailerClass);

        \Mail::shouldReceive('to->queue')
            ->withAnyArgs();
    }

    /**
     * For use with dispatchNow()
     *
     * @param $jobClass
     * @param $return
     *
     * @return Mockery\MockInterface
     */
    public function expectsJobWithReturn($jobClass, $return)
    {
        $mock = Mockery::mock(Dispatcher::class);
        $mock->shouldReceive('dispatchNow')
            ->once()
            ->with($jobClass)
            ->andReturn($return);
        $this->instance(Dispatcher::class, $mock);

        return $mock;
    }

    /**
     * Matches job classes to return values
     *
     * @param array $jobReturnMap
     *
     * @return Mockery\MockInterface
     */
    public function expectsJobsWithReturns($jobReturnMap)
    {
        $mock = Mockery::mock(Dispatcher::class);
        $mock->shouldReceive('dispatchNow')
            ->times(count($jobReturnMap))
            ->with(Mockery::any(array_keys($jobReturnMap)))
            ->andReturnUsing(function($jobObject) use ($jobReturnMap) {
                return (isset($jobReturnMap[get_class($jobObject)])) ? $jobReturnMap[get_class($jobObject)] : null;
            });
        $this->instance(Dispatcher::class, $mock);

        return $mock;
    }

    /**
     * Specify a list of jobs that should NOT be dispatched for the given operation.
     *
     * @param array|dynamic $jobs
     *
     * @return $this
     */
    protected function doesNotExpectJobs($jobs)
    {
        $jobs = is_array($jobs) ? $jobs : func_get_args();

        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);

        foreach ($jobs as $job) {
            $mock->shouldNotReceive('dispatch')->with(Mockery::type($job));
        }

        $this->instance('Illuminate\Contracts\Bus\Dispatcher', $mock);

        return $this;
    }

    /**
     * Get a collection with one random object in it
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDummyCollection()
    {
        return collect([$this->getDummyObject()]);
    }

    /**
     * Get an object with a random property
     *
     * @return object
     */
    protected function getDummyObject()
    {
        return (object) $this->getDummyArray();
    }

    /**
     * Get an array with a random key and value
     *
     * @return array
     */
    protected function getDummyArray()
    {
        $obj                     = [];
        $obj[$this->faker->word] = $this->faker->word;

        return $obj;
    }

    /**
     * @return mixed
     */
    protected function getJsonResponse()
    {
        return json_decode($this->response->getContent());
    }

    /**
     * Create a model factory role
     *
     * @param      $slug
     * @param null $name
     */
    protected function role($slug, $name = null)
    {
        return factory(\App\Models\Role::class)->create(
            [
                'slug' => $slug,
                'name' => ($name) ?: ucwords(camel_case($slug)),
            ]
        );
    }

    protected static function array_selection($array, $keys)
    {
        $result = [];

        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $result[$key] = $array[$key];
            }
        }

        return $result;
    }
}
