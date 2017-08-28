<?php

namespace App\Providers;

use App;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor'   => 'RESBEAT, LLC',
        'product'  => 'RESBEAT',
        'street'   => '3005 S Lamar Blvd Ste D109 #242',
        'location' => 'Austin, TX 78704',
        'phone'    => '512-691-9555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = 'nate.ritter@resbeat.com';

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'nate.ritter@resbeat.com',
        'tadevosyanhrach@gmail.com'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Register custom bindings
     */
    public function register()
    {
        Spark::useUserModel('App\Models\User');
        Spark::useTeamModel('App\Models\Team');
    }

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ]);
    }
}