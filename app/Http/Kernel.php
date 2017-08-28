<?php

namespace App\Http;

use App\Http\Middleware\AuthOrQueryString;
use App\Http\Middleware\BelongsTo\AttachmentBelongsToProposal;
use App\Http\Middleware\BelongsTo\AttachmentBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\ChangeOrderBelongsToContract;
use App\Http\Middleware\BelongsTo\ContractBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\EventBelongsToClient;
use App\Http\Middleware\BelongsTo\EventDateRangeBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\EventDateRangeBelongsToProposalViaProposalDateRange;
use App\Http\Middleware\BelongsTo\HotelBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\LicenseeQuestionBelongsToLicenseeQuestionGroup;
use App\Http\Middleware\BelongsTo\LicenseeTermBelongsToGroup;
use App\Http\Middleware\BelongsTo\NoteBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\ProposalBelongsToProposalRequest;
use App\Http\Middleware\BelongsTo\RequestQuestionBelongsToGroup;
use App\Http\Middleware\BelongsTo\RequestQuestionGroupBelongsToRequest;
use App\Http\Middleware\BelongsTo\ReservationBelongsToContract;
use App\Http\Middleware\BelongsTo\UserBelongsToClient;
use App\Http\Middleware\BelongsTo\UserBelongsToPlanner;
use App\Http\Middleware\BelongsTo\UserBelongsToProposalRequestOnHotelSide;
use App\Http\Middleware\CanAccess\HotelUserCanAccessProposal;
use App\Http\Middleware\CanAccess\ThisHotelUserCanAccessContract;
use App\Http\Middleware\CanAccess\ThisHotelUserCanAccessProposal;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessClause;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessContract;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessEvent;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessProposal;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessProposalRequest;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessQuestionGroup;
use App\Http\Middleware\CanAccess\ThisLicenseeCanAccessTermGroup;
use App\Http\Middleware\ClientContractQueryStringIsAuthentic;
use App\Http\Middleware\ContractIsNotClientOwned;
use App\Http\Middleware\IsLocalEnvironment;
use App\Http\Middleware\ObjectExists\AttachmentExists;
use App\Http\Middleware\ObjectExists\BrandExists;
use App\Http\Middleware\ObjectExists\ChangeOrderExists;
use App\Http\Middleware\ObjectExists\ClauseExists;
use App\Http\Middleware\ObjectExists\ClientExists;
use App\Http\Middleware\ObjectExists\ContractExists;
use App\Http\Middleware\ObjectExists\EventDateRangeExists;
use App\Http\Middleware\ObjectExists\EventExists;
use App\Http\Middleware\ObjectExists\GuestExists;
use App\Http\Middleware\ObjectExists\HotelExists;
use App\Http\Middleware\ObjectExists\LicenseeExists;
use App\Http\Middleware\ObjectExists\LicenseeQuestionExists;
use App\Http\Middleware\ObjectExists\LicenseeQuestionGroupExists;
use App\Http\Middleware\ObjectExists\LicenseeTermExists;
use App\Http\Middleware\ObjectExists\LicenseeTermGroupExists;
use App\Http\Middleware\ObjectExists\PlannerExists;
use App\Http\Middleware\ObjectExists\ProposalExists;
use App\Http\Middleware\ObjectExists\ProposalRequestExists;
use App\Http\Middleware\ObjectExists\RequestNoteExists;
use App\Http\Middleware\ObjectExists\RequestQuestionExists;
use App\Http\Middleware\ObjectExists\RequestQuestionGroupExists;
use App\Http\Middleware\ObjectExists\ReservationExists;
use App\Http\Middleware\ObjectExists\UserExists;
use App\Http\Middleware\ProfileJsonResponse;
use App\Http\Middleware\ProposalQueryString;
use App\Http\Middleware\ProposalQueryStringAuth;
use App\Http\Middleware\ProposalAuthOrQueryString;
use App\Http\Middleware\ProposalRequestQueryString;
use App\Http\Middleware\RequireAuthOrPassword;
use App\Http\Middleware\ThisUserIs\ThisUserHasNoPassword;
use App\Http\Middleware\ThisUserIs\ThisUserHasPassword;
use App\Http\Middleware\ThisUserIs\ThisUserIsHotelUser;
use App\Http\Middleware\ThisUserIs\ThisUserIsLicensee;
use App\Http\Middleware\UserIs\UserIsUnclaimed;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Laravel\Spark\Http\Middleware\CreateFreshApiToken::class,
        ],

        'local' => [
            IsLocalEnvironment::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'auth.require-active-hotel' => \App\Http\Middleware\RequiresActiveHotel::class,
        'dev' => \Laravel\Spark\Http\Middleware\VerifyUserIsDeveloper::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'hasTeam' => \Laravel\Spark\Http\Middleware\VerifyUserHasTeam::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'subscribed' => \Laravel\Spark\Http\Middleware\VerifyUserIsSubscribed::class,
        'teamSubscribed' => \Laravel\Spark\Http\Middleware\VerifyTeamIsSubscribed::class,

        // Debug
        'debugbar' => ProfileJsonResponse::class,

        //  CanAccess Middleware
        'this-hotel-user.can-access.proposal'  => ThisHotelUserCanAccessProposal::class,
        'this-hotel-user.can-access.contract'  => ThisHotelUserCanAccessContract::class,
        'this-licensee.can-access.clause'  => ThisLicenseeCanAccessClause::class,
        'this-licensee.can-access.contract'  => ThisLicenseeCanAccessContract::class,
        'this-licensee.can-access.event'  => ThisLicenseeCanAccessEvent::class,
        'this-licensee.can-access.proposal'  => ThisLicenseeCanAccessProposal::class,
        'this-licensee.can-access.question-group'  => ThisLicenseeCanAccessQuestionGroup::class,
        'this-licensee.can-access.request'  => ThisLicenseeCanAccessProposalRequest::class,
        'this-licensee.can-access.term-group'  => ThisLicenseeCanAccessTermGroup::class,

        //  ThisUserIs middleware
        'user-has.password'     => ThisUserHasPassword::class,
        'user-has-no.password'  => ThisUserHasNoPassword::class,
        'user-is.hotel-user'      => ThisUserIsHotelUser::class,
        'user-is.licensee'      => ThisUserIsLicensee::class,

        //  UserIs middleware
        'user.is-unclaimed' => UserIsUnclaimed::class,

        //  ObjectExists middleware
        'attachment.exists' => AttachmentExists::class,
        'brand.exists' => BrandExists::class,
        'change-order.exists' => ChangeOrderExists::class,
        'clause.exists' => ClauseExists::class,
        'client.exists' => ClientExists::class,
        'contract.exists' => ContractExists::class,
        'event.exists' => EventExists::class,
        'event-date-range.exists' => EventDateRangeExists::class,
        'guest.exists' => GuestExists::class,
        'hotel.exists' => HotelExists::class,
        'licensee.exists' => LicenseeExists::class,
        'licensee-question.exists' => LicenseeQuestionExists::class,
        'licensee-question-group.exists' => LicenseeQuestionGroupExists::class,
        'licensee-term.exists' => LicenseeTermExists::class,
        'licensee-term-group.exists' => LicenseeTermGroupExists::class,
        'planner.exists' => PlannerExists::class,
        'proposal.exists' => ProposalExists::class,
        'proposal-request.exists' => ProposalRequestExists::class,
        'request-note.exists' => RequestNoteExists::class,
        'request-question.exists' => RequestQuestionExists::class,
        'request-question-group.exists' => RequestQuestionGroupExists::class,
        'reservation.exists' => ReservationExists::class,
        'user.exists' => UserExists::class,

        //  BelongsTo middleware
        'attachment.belongs-to.proposal' => AttachmentBelongsToProposal::class,
        'attachment.belongs-to.request' => AttachmentBelongsToProposalRequest::class,
        'change-order.belongs-to.contract'   => ChangeOrderBelongsToContract::class,
        'contract.belongs-to.request'   => ContractBelongsToProposalRequest::class,
        'event.belongs-to.client'   => EventBelongsToClient::class,
        'event-date-range.belongs-to.request' => EventDateRangeBelongsToProposalRequest::class,
        'event-date-range.belongs-to.proposal.via.proposal-date-range' => EventDateRangeBelongsToProposalViaProposalDateRange::class,
        'licensee-question.belongs-to.licensee-question-group'   => LicenseeQuestionBelongsToLicenseeQuestionGroup::class,
        'licensee-term.belongs-to.licensee-term-group'   => LicenseeTermBelongsToGroup::class,
        'hotel.belongs-to.request'   => HotelBelongsToProposalRequest::class,
        'note.belongs-to.request'   => NoteBelongsToProposalRequest::class,
        'proposal.belongs-to.request'   => ProposalBelongsToProposalRequest::class,
        'request-question-group.belongs-to.request'   => RequestQuestionGroupBelongsToRequest::class,
        'request-question.belongs-to.group'   => RequestQuestionBelongsToGroup::class,
        'reservation.belongs-to.contract'   => ReservationBelongsToContract::class,
        'user.belongs-to.request.on.hotel-side' => UserBelongsToProposalRequestOnHotelSide::class,
        'user.belongs-to.client' => UserBelongsToClient::class,
        'user.belongs-to.planner' => UserBelongsToPlanner::class,

        //  Other Custom
        'contract.is-not-client-owned'  => ContractIsNotClientOwned::class,
        'contract.client-query-string'  => ClientContractQueryStringIsAuthentic::class,
        'proposal.auth-or-query-string' => ProposalAuthOrQueryString::class,
        'proposal-request.query-string' => ProposalRequestQueryString::class,
        'proposal.query-string' => ProposalQueryString::class,
    ];
}
