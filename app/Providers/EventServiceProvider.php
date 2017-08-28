<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // User Related Events...
        'Laravel\Spark\Events\Auth\UserRegistered' => [
            'Laravel\Spark\Listeners\Subscription\CreateTrialEndingNotification',
        ],

        'Laravel\Spark\Events\Subscription\UserSubscribed' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
            'Laravel\Spark\Listeners\Subscription\UpdateTrialEndingDate',
        ],

        'Laravel\Spark\Events\Profile\ContactInformationUpdated' => [
            'Laravel\Spark\Listeners\Profile\UpdateContactInformationOnStripe',
        ],

        'Laravel\Spark\Events\PaymentMethod\VatIdUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'Laravel\Spark\Events\PaymentMethod\BillingAddressUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'Laravel\Spark\Events\Subscription\SubscriptionUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Subscription\SubscriptionCancelled' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
        ],

        // Team Related Events...
        'Laravel\Spark\Events\Teams\TeamCreated' => [
            'Laravel\Spark\Listeners\Teams\Subscription\CreateTrialEndingNotification',
        ],

        'Laravel\Spark\Events\Teams\Subscription\TeamSubscribed' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateTrialEndingDate',
        ],

        'Laravel\Spark\Events\Teams\Subscription\SubscriptionUpdated' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Teams\Subscription\SubscriptionCancelled' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Teams\UserInvitedToTeam' => [
            'Laravel\Spark\Listeners\Teams\CreateInvitationNotification',
        ],

        //  Misc
        'App\Events\EmailWasSent'   => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],

        //  All Attachments
        'App\Events\Attachment\AttachmentWasStored' => [],
        'App\Events\Attachment\AttachmentWasDeleted' => [],

        //  User events
        'App\Events\User\UserSubmittedProposal' => [],
        'App\Events\User\UserWasAssignedFirstPassword' => [
            'App\Listeners\Jobs\User\ListenThenAutoAuthenticateUser',
            'App\Listeners\Mail\Hotel\ListenThenSendHotelUserFirstPassword',
        ],
        'App\Events\User\UserPasswordWasConfirmed' => [
            'App\Listeners\Jobs\User\ListenThenAutoAuthenticateUser',
        ],
        'App\Events\User\UserWasAutomaticallyAuthenticated' => [],
        'App\Events\User\UserWasDetachedFromClient' => [],
        'App\Events\User\UserWasDetachedFromPlanner' => [],

        'App\Events\Hotel\HotelImageWasAdded'   => [],


        //  Hotel events
        'App\Events\Hotel\Proposal\ProposalWasUpdated' => [],
        'App\Events\Hotel\Proposal\ProposalWasDeclined' => [
            'App\Listeners\Mail\Proposal\ListenThenSendProposalDeclinedToHotel',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Hotel\Proposal\ProposalWasCreated' => [],
        'App\Events\Hotel\Proposal\ProposalWasSubmitted' => [
            'App\Listeners\Mail\Proposal\ListenThenSendProposalSubmittedToLicenseeStaff',
            'App\Listeners\Mail\Proposal\ListenThenSendProposalSubmittedToHotelUsers',
            'App\Listeners\Mail\Proposal\ListenThenSendWelcomeIfFirstSubmission',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Hotel\Proposal\ProposalDeclineWasReset' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],

        //  Admin events
        'App\Events\Admin\Hotel\HotelWasCreated' => [],
        'App\Events\Admin\Hotel\HotelWasDeleted' => [],
        'App\Events\Admin\Hotel\HotelWasUpdated' => [],
        'App\Events\Admin\Licensee\LicenseeWasCreated' => [],
        'App\Events\Admin\User\LicenseeUserWasCreated' => [
            'App\Listeners\Mail\Licensee\ListenThenSendWelcomeToLicenseeUser',
        ],
        'App\Events\Admin\User\TempUserWasCreated' => [],
        'App\Events\Admin\User\UserWasDeleted' => [],

        //  --- Change Order
        'App\Events\ChangeOrder\ChangeOrderWasCreated' => [
            'App\Listeners\Mail\ChangeOrder\ListenThenSendChangeOrderCreatedEmailToReceivingParty',
            'App\Listeners\Mail\ChangeOrder\ListenThenSendChangeOrderCreatedEmailToIssuingParty',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\ChangeOrder\ChangeOrderItemWasDeclined' => [],
        'App\Events\ChangeOrder\ChangeOrderItemWasAccepted' => [],
        'App\Events\ChangeOrder\OfflineChangeOrderSetWasProcessed' => [],
        'App\Events\ChangeOrder\ChangeOrderSetWasProcessed' => [
            'App\Listeners\Mail\ChangeOrder\ListenThenSendChangeOrderProcessedEmailToIssuingParty',
            'App\Listeners\Mail\ChangeOrder\ListenThenSendChangeOrderProcessedEmailToReceivingParty',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],

        //  --- Proposal
        'App\Events\Hotel\Proposal\ProposalWasVisitedByHotel' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\Proposal\ProposalWasVisitedByLicensee' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Hotel\ProposalCutoffIsApproaching' => [
            'App\Listeners\Mail\Proposal\ListenThenSendProposalExpiringReminder',
        ],

        //  --- Contract
        'App\Events\Licensee\Contract\OfflineContractWasInitialized' => [],
        'App\Events\Licensee\Contract\ContractWasConvertedToOffline' => [],
        'App\Events\Licensee\Contract\ClientSignatureWasRequested' => [
            'App\Listeners\Mail\Contract\ListenThenSendSignatureRequestToClient',
        ],
        'App\Events\Licensee\Contract\ContractOwnershipWasTransferredToClient' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\Contract\ContractOwnershipWasRevokedFromClient' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Admin\Contract\ContractWasCreated' => [
            'App\Listeners\Mail\Contract\ListenThenSendContractWasCreatedToLicenseeStaff',
            'App\Listeners\Mail\Contract\ListenThenSendContractWasCreatedToHotelUsers',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Admin\Contract\ContractWasDeclined' => [],
        'App\Events\Admin\Contract\ContractWasAccepted' => [
            'App\Listeners\Mail\Contract\ListenThenSendContractAcceptanceToOtherParty',
            'App\Listeners\Mail\Contract\ListenThenSendContractAcceptanceToAcceptingParty',
            'App\Listeners\Jobs\Contract\ListenThenSnapshotContractData',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Admin\Contract\DeclinedContractWasReset' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Admin\Contract\ContractSnapshotWasCaptured' => [],

        'App\Events\Licensee\Contract\ContractWasVisitedByHotel' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\Contract\ContractWasVisitedByLicensee' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\Contract\ContractWasVisitedByClient' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],

        //  Licensee events

        //  --- Weekly Summary
        'App\Events\Licensee\WeeklySummaryIsNeeded' => [
            'App\Listeners\Mail\Licensee\ListenThenSendWeeklySummary',
        ],
        'App\Events\Licensee\DailySummaryIsNeeded' => [
            'App\Listeners\Mail\Licensee\ListenThenSendDailySummary',
        ],

        //  --- Timezones
        'App\Events\Licensee\TimezoneDataWasAdjustedForRFPsAndProposals' => [],
        'App\Events\Licensee\TimezoneWasChanged' => [
            'App\Listeners\Jobs\Licensee\ListenThenUpdateObjectsForTimezoneChange',
        ],

        //  --- Brand Contact
        'App\Events\Licensee\BrandContact\BrandContactWasUnlinked' => [],

        //  --- Clause
        'App\Events\Licensee\Clause\ClauseWasCreated'   => [],
        'App\Events\Licensee\Clause\ClauseWasDeleted'   => [],
        'App\Events\Licensee\Clause\ClauseWasUpdated'   => [],

        //  --- Client
        'App\Events\Licensee\Client\ClientWasCreated'   => [],
        'App\Events\Licensee\Client\ClientWasUpdated'   => [],

        //  --- Event
        'App\Events\Licensee\Event\EventWasCreated'   => [],
        'App\Events\Licensee\Event\EventWasUpdated'   => [],

        //  --- EventDateRange
        'App\Events\Licensee\EventDateRange\EventDateRangeWasCreated'   => [],
        'App\Events\Licensee\EventDateRange\EventDateRangeWasUpdated'   => [],
        'App\Events\Licensee\EventDateRange\EventDateRangeWasDeleted'   => [],

        //  --- EventGroup
        'App\Events\Licensee\EventGroup\EventGroupWasCreated'   => [],

        'App\Events\Licensee\Hotel\HotelGoogleDataWasUpdated'   => [],

        //  --- Event Locations
        'App\Events\Licensee\EventLocation\EventLocationsWereSynced'   => [],

        //  --- Guests
        'App\Events\Licensee\Reservation\GuestWasAddedToReservation'   => [],
        'App\Events\Licensee\Reservation\GuestWasCreated'   => [],
        'App\Events\Licensee\Reservation\GuestWasUpdated'   => [],
        'App\Events\Licensee\Reservation\GuestWasDeleted'   => [],

        //  --- Licensee Questions
        'App\Events\Licensee\Questions\LicenseeQuestionWasCreated'   => [],
        'App\Events\Licensee\Questions\LicenseeQuestionWasUpdated'   => [],
        'App\Events\Licensee\Questions\LicenseeQuestionWasDeleted'   => [],

        //  --- Licensee Question Groups
        'App\Events\Licensee\QuestionGroups\LicenseeQuestionGroupWasCreated'   => [],
        'App\Events\Licensee\QuestionGroups\LicenseeQuestionGroupWasUpdated'   => [],
        'App\Events\Licensee\QuestionGroups\LicenseeQuestionGroupWasDeleted'   => [],

        //  --- Licensee Terms
        'App\Events\Licensee\Terms\LicenseeTermWasCreated'   => [],
        'App\Events\Licensee\Terms\LicenseeTermWasUpdated'   => [],
        'App\Events\Licensee\Terms\LicenseeTermWasDeleted'   => [],

        //  --- Licensee Term Groups
        'App\Events\Licensee\TermGroups\LicenseeTermGroupWasCreated'   => [],
        'App\Events\Licensee\TermGroups\LicenseeTermGroupWasUpdated'   => [],
        'App\Events\Licensee\TermGroups\LicenseeTermGroupWasDeleted'   => [],

        //  --- LogEntry
        'App\Events\LogEntry\EventWasLogged' => [],

        //  --- Planner
        'App\Events\Licensee\Planner\PlannerWasCreated'   => [],

        //  --- Profile
        'App\Events\Licensee\Profile\LicenseeProfileWasUpdated' => [],
        'App\Events\Licensee\Profile\LicenseeLogoWasUpdated' => [],

        //  --- Proposal Request Admin
        'App\Events\Licensee\ProposalRequestCutoffIsApproaching' => [
            'App\Listeners\Mail\ProposalRequest\ListenThenSendProposalRequestExpiringReminder',
        ],
        'App\Events\Licensee\ProposalRequestWasCreated' => [
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\ProposalRequestWasDeleted' => [],
        'App\Events\Licensee\ProposalRequestWasDuplicated' => [],
        'App\Events\Licensee\ProposalRequest\EventDetailsWereUpdated' => [],
        'App\Events\Licensee\ProposalRequest\RoomRequestDatesWereUpdated' => [],
        'App\Events\Licensee\ProposalRequest\AccommodationsWereUpdated' => [],
        'App\Events\Licensee\ProposalRequest\SpacesWereUpdated' => [],
        'App\Events\Licensee\ProposalRequest\FoodBeverageWasUpdated' => [],
        'App\Events\Licensee\ProposalRequest\ProposalRequestsWereDisbursed' => [
            'App\Listeners\Mail\ProposalRequest\ListenThenSendDisbursalConfirmationToStaff',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],
        'App\Events\Licensee\ProposalRequest\UserWasAddedToProposalRequest' => [],
        'App\Events\Licensee\Proposal\UserWasAddedToProposal' => [],
        'App\Events\Licensee\Proposal\UserWasRemovedFromProposal' => [],

        //  --- Recipients of Proposal Requests
        'App\Events\Licensee\ProposalRequest\UserContactWasInitiatedForProposalRequest' => [
            'App\Listeners\Mail\ProposalRequest\ListenThenSendDisbursementEmailToRecipients',
            'App\Listeners\Jobs\LogEntry\ListenThenLogEvent',
        ],

        //  --- Proposal Request Notes
        'App\Events\Licensee\RequestNote\RequestNoteWasCreated' => [],
        'App\Events\Licensee\RequestNote\RequestNoteWasUpdated' => [],
        'App\Events\Licensee\RequestNote\RequestNoteWasDeleted' => [],

        //  --- Proposal Request Actions
        'App\Events\Hotel\Proposal\ProposalActionOccurred' => [
            'App\Listeners\Jobs\Proposal\ListenThenLogProposalAction',
        ],
        'App\Events\Hotel\Proposal\ProposalActionWasLogged' => [],

        //  --- Proposal Request Hotels
        'App\Events\Licensee\ProposalRequest\HotelWasAddedToProposalRequest' => [],
        'App\Events\Licensee\ProposalRequest\HotelWasRemovedFromProposalRequest' => [],

        //  --- Proposal Request Recipients
        'App\Events\Licensee\ProposalRequest\RecipientWasAddedToProposalRequest' => [],
        'App\Events\Licensee\ProposalRequest\RecipientWasRemovedFromProposalRequest' => [],

        //  --- Proposal Request Question Groups
        'App\Events\Licensee\ProposalRequest\RequestQuestionGroupWasCreated'   => [],
        'App\Events\Licensee\ProposalRequest\RequestQuestionGroupWasUpdated'   => [],
        'App\Events\Licensee\ProposalRequest\RequestQuestionGroupWasDeleted'   => [],

        //  --- Proposal Request Questions
        'App\Events\Licensee\ProposalRequest\RequestQuestionWasCreated'   => [],
        'App\Events\Licensee\ProposalRequest\RequestQuestionWasUpdated'   => [],
        'App\Events\Licensee\ProposalRequest\RequestQuestionWasDeleted'   => [],

        'App\Events\User\UserProfileWasUpdated'   => [],

        //  --- Reservations
        'App\Events\Licensee\Reservation\ReservationWasCreated' => [],
        'App\Events\Licensee\Reservation\ReservationWasUpdated' => [],

    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
