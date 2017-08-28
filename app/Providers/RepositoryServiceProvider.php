<?php

namespace App\Providers;

use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use App\Repositories\Contracts\EventLocationRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Map of contracts and repos
     *
     * @var array
     */
    private $pairs = [
        \App\Repositories\Contracts\AmenityRepositoryInterface::class => \App\Repositories\Eloquent\AmenityRepository::class,
        \App\Repositories\Contracts\AmenityTypeRepositoryInterface::class => \App\Repositories\Eloquent\AmenityTypeRepository::class,
        \App\Repositories\Contracts\AttachmentRepositoryInterface::class => \App\Repositories\Eloquent\AttachmentRepository::class,
        \App\Repositories\Contracts\AttributeRepositoryInterface::class => \App\Repositories\Eloquent\AttributeRepository::class,
        \App\Repositories\Contracts\BrandRepositoryInterface::class => \App\Repositories\Eloquent\BrandRepository::class,
        \App\Repositories\Contracts\ChangeOrderRepositoryInterface::class => \App\Repositories\Eloquent\ChangeOrderRepository::class,
        \App\Repositories\Contracts\ClauseRepositoryInterface::class => \App\Repositories\Eloquent\ClauseRepository::class,
        \App\Repositories\Contracts\ClientRepositoryInterface::class => \App\Repositories\Eloquent\ClientRepository::class,
        \App\Repositories\Contracts\ContractRepositoryInterface::class => \App\Repositories\Eloquent\ContractRepository::class,
        \App\Repositories\Contracts\ContractTermGroupRepositoryInterface::class => \App\Repositories\Eloquent\ContractTermGroupRepository::class,
        \App\Repositories\Contracts\ContractTermRepositoryInterface::class => \App\Repositories\Eloquent\ContractTermRepository::class,
        \App\Repositories\Contracts\EventRepositoryInterface::class => \App\Repositories\Eloquent\EventRepository::class,
        \App\Repositories\Contracts\EventTypeRepositoryInterface::class => \App\Repositories\Eloquent\EventTypeRepository::class,
        \App\Repositories\Contracts\EventDateRangeRepositoryInterface::class => \App\Repositories\Eloquent\EventDateRangeRepository::class,
        \App\Repositories\Contracts\EventGroupRepositoryInterface::class => \App\Repositories\Eloquent\EventGroupRepository::class,
        \App\Repositories\Contracts\EventLocationRepositoryInterface::class => \App\Repositories\Eloquent\EventLocationRepository::class,
        \App\Repositories\Contracts\GuestRepositoryInterface::class => \App\Repositories\Eloquent\GuestRepository::class,
        \App\Repositories\Contracts\HotelRepositoryInterface::class => \App\Repositories\Eloquent\HotelRepository::class,
        \App\Repositories\Contracts\HotelImageRepositoryInterface::class => \App\Repositories\Eloquent\HotelImageRepository::class,
        \App\Repositories\Contracts\HotelCorrelationRepositoryInterface::class => \App\Repositories\Eloquent\HotelCorrelationRepository::class,
        \App\Repositories\Contracts\LogEntryRepositoryInterface::class => \App\Repositories\Eloquent\LogEntryRepository::class,
        \App\Repositories\Contracts\LicenseeRepositoryInterface::class => \App\Repositories\Eloquent\LicenseeRepository::class,
        \App\Repositories\Contracts\LicenseeQuestionRepositoryInterface::class => \App\Repositories\Eloquent\LicenseeQuestionRepository::class,
        \App\Repositories\Contracts\LicenseeQuestionGroupRepositoryInterface::class => \App\Repositories\Eloquent\LicenseeQuestionGroupRepository::class,
        \App\Repositories\Contracts\LicenseeTermRepositoryInterface::class => \App\Repositories\Eloquent\LicenseeTermRepository::class,
        \App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface::class => \App\Repositories\Eloquent\LicenseeTermGroupRepository::class,
        \App\Repositories\Contracts\PaymentMethodRepositoryInterface::class => \App\Repositories\Eloquent\PaymentMethodRepository::class,
        \App\Repositories\Contracts\PlannerRepositoryInterface::class => \App\Repositories\Eloquent\PlannerRepository::class,
        \App\Repositories\Contracts\PropertyTypeRepositoryInterface::class => \App\Repositories\Eloquent\PropertyTypeRepository::class,
        \App\Repositories\Contracts\ProposalDateRangeRepositoryInterface::class => \App\Repositories\Eloquent\ProposalDateRangeRepository::class,
        \App\Repositories\Contracts\ProposalRepositoryInterface::class => \App\Repositories\Eloquent\ProposalRepository::class,
        \App\Repositories\Contracts\ProposalRequestRepositoryInterface::class => \App\Repositories\Eloquent\ProposalRequestRepository::class,
        \App\Repositories\Contracts\ProposalActionRepositoryInterface::class => \App\Repositories\Eloquent\ProposalActionRepository::class,
        \App\Repositories\Contracts\ReservationRepositoryInterface::class => \App\Repositories\Eloquent\ReservationRepository::class,
        \App\Repositories\Contracts\ReservationMethodRepositoryInterface::class => \App\Repositories\Eloquent\ReservationMethodRepository::class,
        \App\Repositories\Contracts\RequestHotelRepositoryInterface::class => \App\Repositories\Eloquent\RequestHotelRepository::class,
        \App\Repositories\Contracts\RequestNoteRepositoryInterface::class => \App\Repositories\Eloquent\RequestNoteRepository::class,
        \App\Repositories\Contracts\RequestQuestionRepositoryInterface::class => \App\Repositories\Eloquent\RequestQuestionRepository::class,
        \App\Repositories\Contracts\RequestQuestionGroupRepositoryInterface::class => \App\Repositories\Eloquent\RequestQuestionGroupRepository::class,
        \App\Repositories\Contracts\RoleRepositoryInterface::class => \App\Repositories\Eloquent\RoleRepository::class,
        \App\Repositories\Contracts\RoomRequestDateRepositoryInterface::class => \App\Repositories\Eloquent\RoomRequestDateRepository::class,
        \App\Repositories\Contracts\RoomSetRepositoryInterface::class => \App\Repositories\Eloquent\RoomSetRepository::class,
        \App\Repositories\Contracts\SpaceRequestRepositoryInterface::class => \App\Repositories\Eloquent\SpaceRequestRepository::class,
        \App\Repositories\Contracts\TagRepositoryInterface::class => \App\Repositories\Eloquent\TagRepository::class,
        \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\UserRepository::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->pairs as $interface => $class) {
            $matches = [];
            preg_match('/([a-zA-Z]+)RepositoryInterface/', $interface, $matches);
            $model = sprintf('\App\Models\%s', $matches[1]);
            $this->app->bind($interface, function ($app) use ($model, $class) {
                return new $class(new $model);
            });
        }

        $this->app->bind(\App\Repositories\Contracts\HotelSearchRepositoryInterface::class, function ($app) {
            return new \App\Repositories\Eloquent\HotelSearchRepository(
                new \App\Models\Hotel(),
                $app->make(EventLocationRepositoryInterface::class),
                $app->make(BrandRepositoryInterface::class)
            );
        });
    }
}
