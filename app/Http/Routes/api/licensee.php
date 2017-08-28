<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes meant to be accessed via AJAX call. Each of these returns
| a JSON response
|
*/
use App\Models\RequestNote;

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function () {

    //  Licensee-side
    Route::group([
        'prefix' => 'licensee',
        'middleware' => [
            'user-is.licensee',
        ],
    ], function () {

        //  Contracts
        Route::post('contracts/{contractId}/accept', [
            'as'        => 'api.licensee.contracts.accept',
            'uses'          => 'Licensee\Contract\AcceptContractsController@store',
            'middleware'    => [
                'contract.exists',
                'this-licensee.can-access.contract',
                'contract.is-not-client-owned',
            ]
        ]);

        //  Clauses
        Route::get('clauses', [
            'as'        => 'api.licensee.clauses.index',
            'uses'      => 'Licensee\ClausesController@index',
        ]);
        Route::post('clauses', [
            'as'        => 'api.licensee.clauses.store',
            'uses'      => 'Licensee\ClausesController@store',
        ]);
        Route::put('clauses/{clauseId}', [
            'as'        => 'api.licensee.clauses.update',
            'uses'      => 'Licensee\ClausesController@update',
            'middleware' => [
                'clause.exists',
                'this-licensee.can-access.clause',
            ],
        ]);
        Route::delete('clauses/{clauseId}', [
            'as'        => 'api.licensee.clauses.destroy',
            'uses'      => 'Licensee\ClausesController@destroy',
            'middleware' => [
                'clause.exists',
                'this-licensee.can-access.clause',
            ],
        ]);

        //  Clients and planners
        Route::get('clients', [
            'as'        => 'api.clients.index',
            'uses'      => 'Licensee\ClientsController@index',
        ]);
        Route::post('clients/{placeId}', [
            'as'        => 'api.clients.store',
            'uses'      => 'Licensee\ClientsController@store',
        ]);
        Route::put('clients/{placeId}', [
            'as'        => 'api.clients.update',
            'uses'      => 'Licensee\ClientsController@update',
        ]);

        //  Change Orders
        Route::get('contracts/{contractId}/change-orders', [
            'as'        => 'api.licensee.change-orders.index',
            'uses'      => 'Licensee\Contract\ChangeOrdersController@index',
            'middleware'    => [
                'contract.exists',
                'this-licensee.can-access.contract',
            ],
        ]);
        Route::post('contracts/{contractId}/change-orders', [
            'as'        => 'api.licensee.change-orders.offline-store',
            'uses'      => 'Licensee\Contract\ChangeOrdersController@store',
            'middleware'    => [
                'contract.exists',
                'this-licensee.can-access.contract',
            ],
        ]);
        Route::post('contracts/{contractId}/change-orders/{changeOrderId}/respond', [
            'as'        => 'api.licensee.change-orders.respond',
            'uses'      => 'Licensee\Contract\ChangeOrderResponsesController@store',
            'middleware'    => [
                'contract.exists',
                'this-licensee.can-access.contract',
                'change-order.exists',
                'contract.is-not-client-owned',
                'change-order.belongs-to.contract',
            ]
        ]);

        //  Reservation Guests
        Route::post('guests', [
            'as'        => 'api.licensee.guests.store',
            'uses'      => 'Licensee\GuestsController@store',
        ]);
        Route::get('guests/{guestId}', [
            'as'        => 'api.licensee.guests.show',
            'uses'      => 'Licensee\GuestsController@show',
            'middleware'    => [
                'guest.exists',
            ],
        ]);
        Route::put('guests/{guestId}', [
            'as'        => 'api.licensee.guests.update',
            'uses'      => 'Licensee\GuestsController@update',
            'middleware'    => [
                'guest.exists',
            ],
        ]);
        Route::delete('guests/{guestId}', [
            'as'        => 'api.licensee.guests.destroy',
            'uses'      => 'Licensee\GuestsController@destroy',
            'middleware'    => [
                'guest.exists',
            ],
        ]);

        //  Term Groups
        Route::get('term-groups', [
            'as'            => 'api.licensee.term-groups.index',
            'uses'          => 'Licensee\TermGroupsController@index',
        ]);
        Route::post('term-groups', [
            'as'            => 'api.licensee.term-groups.store',
            'uses'          => 'Licensee\TermGroupsController@store',
        ]);
        Route::put('term-groups/{groupId}', [
            'as'            => 'api.licensee.term-groups.update',
            'uses'          => 'Licensee\TermGroupsController@update',
            'middleware' => [
                'licensee-term-group.exists',
                'this-licensee.can-access.term-group',
            ],
        ]);
        Route::delete('term-groups/{groupId}', [
            'as'            => 'api.licensee.term-groups.destroy',
            'uses'          => 'Licensee\TermGroupsController@destroy',
            'middleware' => [
                'licensee-term-group.exists',
                'this-licensee.can-access.term-group',
            ],
        ]);

        //  Terms
        Route::post('term-groups/{groupId}/terms', [
            'as'            => 'api.licensee.term.store',
            'uses'          => 'Licensee\TermsController@store',
            'middleware' => [
                'licensee-term-group.exists',
                'this-licensee.can-access.term-group',
            ],
        ]);
        Route::put('term-groups/{groupId}/terms/{termId}', [
            'as'            => 'api.licensee.term.update',
            'uses'          => 'Licensee\TermsController@update',
            'middleware' => [
                'licensee-term-group.exists',
                'this-licensee.can-access.term-group',
                'licensee-term.exists',
                'licensee-term.belongs-to.licensee-term-group',
            ],
        ]);
        Route::delete('term-groups/{groupId}/terms/{termId}', [
            'as'            => 'api.licensee.term.destroy',
            'uses'          => 'Licensee\TermsController@destroy',
            'middleware' => [
                'licensee-term-group.exists',
                'this-licensee.can-access.term-group',
                'licensee-term.exists',
                'licensee-term.belongs-to.licensee-term-group',
            ],
        ]);
    });
});
