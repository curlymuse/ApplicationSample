<?php

if (! function_exists('diffTimezone')) {

    /**
     * Determine if converting from one timezone to another will necessitate a change
     * in existing date (NOT datetime) data, and if so, what is the shift value
     *
     * @param $from
     * @param $to
     *
     * @return int
     */
    function diffTimezone($from, $to)
    {
        $dummyDate = '1985-11-24';

        $originalTimezoneDate = (new \Carbon\Carbon($dummyDate . ' 23:59:59', $from))->timezone('UTC');
        $newTimezoneDate = (new \Carbon\Carbon($dummyDate . ' 23:59:59', $to))->timezone('UTC');

        return $originalTimezoneDate->diffInHours($newTimezoneDate, false);
    }

}

if (! function_exists('dateToUTC')) {

    /**
     * Convert this string from the supplied timezone to UTC
     *
     * @param $parsable
     * @param $fromTimezone
     *
     * @return string
     */
    function dateToUTC($parsable, $fromTimezone)
    {
        $date = new \Carbon\Carbon($parsable . ' 23:59:59', $fromTimezone);
        $date = $date->tz('UTC')->format('Y-m-d H:i:s');

        return $date;
    }

}

if (! function_exists('timezone')) {

    /**
     * Change this date to the given timezone, with the given format
     *
     * @param string $parsable
     * @param string $timezone
     * @param string $format
     *
     * @return string
     */
    function timezone($parsable, $timezone, $format)
    {
        return Carbon\Carbon::parse($parsable)->timezone($timezone)->format($format);
    }

}

if (!function_exists('json_decode_keys')) {

    /**
     * JSON decode multiple keys in an array
     *
     * @param array $array
     * @param array $keys
     *
     * @return mixed
     */
    function json_decode_keys($array, $keys = []) {
        foreach ($keys as $key) {
            if (! isset($array[$key])) {
                continue;
            }
            $array[$key] = json_decode($array[$key]);
        }

        return $array;
    }

}

if (!function_exists('to_array_deep')) {

    /**
     * Convert an object to an array "deeply" using recursion
     *
     * @param $object
     *
     * @return array
     */
    function to_array_deep($object) {
        $array = (array)($object);
        foreach ($array as &$item) {
            if (is_object($item)) {
                $item = to_array_deep($item);
            } else if (is_array($item)) {
                foreach ($item as &$subItem) {
                    $subItem = to_array_deep($subItem);
                }
            }
        }
        return $array;
    }

}

if (!function_exists('array_dot_deep')) {

    /**
     * Access an object as an array "deeply" using recursion
     *
     * @param $object
     *
     * @return array
     */
    function array_dot_deep($object) {
        $array = array_dot($object);
        foreach ($array as &$item) {
            if (is_object($item)) {
                $item = array_dot_deep($item);
            }
        }
        return array_dot($array);
    }

}

if (!function_exists('calculateDistanceUsingCoordinates')) {

    /**
     * Calculate the distance between lat/long coordinates
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @param string $unit
     *
     * @return float
     */
    function calculateDistanceUsingCoordinates($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "KM") {
            return ($miles * 1.609344);
        } else if ($unit == "NM") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}

if (!function_exists('merge_collections')) {

    /**
     * Merge multiple collections together into a single collection,
     * regardless of the object types of each collection
     *
     * @param $arrayOfCollections
     *
     * @return \Illuminate\Support\Collection
     */
    function merge_collections($arrayOfCollections)
    {
        $collections = (is_array($arrayOfCollections)) ? $arrayOfCollections : func_get_args();

        $return = collect([]);

        foreach ($collections as $collection) {
            $collection->each(function ($object) use (&$return) {
                $return->push($object);
            });
        }

        return $return;
    }

}

if (! function_exists('licenseeId')) {

    /**
     * Get the currently logged in licensee's ID
     *
     * @return int
     */
    function licenseeId()
    {
        $user = \Auth::user();

        $licenseeId = Illuminate\Support\Facades\Cache::remember('licensee-id-'.$user->id, 60, function () use ($user) {
            return $user
                ->roles()
                ->whereRolableType(\App\Models\Licensee::class)
                ->first()
                ->pivot
                ->rolable_id;
        });

        return $licenseeId;
    }

}

if (! function_exists('hotelId')) {

    /**
     * Get the currently logged in hotel's ID
     *
     * @return int
     */
    function hotelId()
    {
        return session()->get('hotel.id');
    }

}

if (! function_exists('userIdToName')) {

    /**
     * Get the user's name with this ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function userIdToName($id)
    {
        return \App\Models\User::findOrFail($id)->name;
    }

}

if (! function_exists('hotelIdToName')) {

    /**
     * Get the hotel's name with this ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function hotelIdToName($id)
    {
        return \App\Models\Hotel::findOrFail($id)->name;
    }

}

if (! function_exists('licenseeIdToName')) {

    /**
     * Get the licensee's name with this ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function licenseeIdToName($id)
    {
        return \App\Models\Licensee::findOrFail($id)->company_name;
    }

}

if (! function_exists('clientIdToName')) {

    /**
     * Get the client's name with this ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function clientIdToName($id)
    {
        return \App\Models\Client::findOrFail($id)->name;
    }

}

if (! function_exists('proposalIdToEventName')) {

    /**
     * Get the events's name with this proposal ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function proposalIdToEventName($id)
    {
        return \App\Models\Event::join('proposal_requests', 'events.id', '=', 'proposal_requests.event_id')
            ->join('proposals', 'proposal_requests.id', '=', 'proposals.proposal_request_id')
            ->where('proposals.id', $id)
            ->firstOrFail()
            ->name;
    }

}

if (! function_exists('contractIdToEventName')) {

    /**
     * Get the events's name with this contract ID
     *
     * @param int $id
     *
     * @return mixed
     */
    function contractIdToEventName($id)
    {
        return \App\Models\Event::join('proposal_requests', 'events.id', '=', 'proposal_requests.event_id')
            ->join('proposals', 'proposal_requests.id', '=', 'proposals.proposal_request_id')
            ->join('contracts', 'proposals.id', '=', 'contracts.proposal_id')
            ->where('contracts.id', $id)
            ->firstOrFail()
            ->name;
    }

}

if (! function_exists('userFromAuthOrQueryString')) {

    /**
     * Get the current authenticated user, or the user who matches
     * the proposal or contact's user ID and hash from the query string
     *
     * @param $objectType
     *
     * @return \App\Models\User
     */
    function userFromAuthOrQueryString($objectType = \App\Models\ProposalRequest::class)
    {
        if (Auth::user()) {
            return Auth::user();
        }

        $userKey = config('resbeat.urls.query-string-user-key');
        $hashKey = config('resbeat.urls.query-string-hash-key');

        $userRepo = app(\App\Repositories\Contracts\UserRepositoryInterface::class);

        $method = ($objectType == \App\Models\ProposalRequest::class) ?
            'findUsingProposalRequestAndUserHash' :
            'findUsingProposalAndUserHash';
        $routeKey = ($objectType == \App\Models\ProposalRequest::class) ?
            'requestId' :
            'proposalId';

        $user = $userRepo->$method(
            \Request::get($userKey),
            \Request::route($routeKey),
            \Request::get($hashKey)
        );

        return $user;
    }
}

if (! function_exists('userId')) {

    /**
     * Get the currently logged in user's ID
     *
     * @return int
     */
    function userId()
    {
        return Auth::user()->id;
    }

}

if (! function_exists('userHasRole')) {

    /**
     * Check if the currently logged in user's role is listed
     * within a given array of parameters.
     *
     * TODO: Explore whether we need to support multiple roles for one user.
     *
     * @param  array   $arrayOfRoles
     *
     * @return boolean
     */
    function userHasRole($arrayOfRoles = [])
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        $userRoles = Cache::remember('roles-user-'.$user->id, 60, function () use ($user) {
            return $user->roles()->get();
        });

        if (empty($userRoles->toArray())) {
            return false;
        }

        $userRole = $userRoles[0]->slug;

        return in_array($userRole, $arrayOfRoles);
    }
}

if (! function_exists('roleUrlPrefix')) {

    /**
     * Get the prefix for urls based on the role the user is logged in as
     *
     * TODO: Explore whether we need to support multiple roles for one user.
     *
     * @return  string Root URL path like 'licensees' or 'hotels' to be used in URLs
     */
    function roleUrlPrefix()
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        $userRoles = Cache::remember('roles-user-'.$user->id, 60, function () use ($user) {
            return $user->roles()->get();
        });

        if (empty($userRoles->toArray())) {
            return false;
        }

        return roleCompanyType($userRoles[0]->slug);
    }
}

if (! function_exists('roleCompanyType')) {
    /**
     * Get the company type of the given role.
     *
     * Company types are licensees, hotels, admin, clients, planners, guests, independent-contractors etc.
     *
     * @param  mixed $role Role slug (or role object) for which the company type is to be determined
     *
     * @return mixed  False on failure or the company type.
     */
    function roleCompanyType($role)
    {
        if (is_object($role) && isset($role->slug)) {
            $role = $role->slug;
        }

        $map = [
            'admin'                  => 'admin',
            'licensee-admin'         => 'licensees',
            'licensee-staff'         => 'licensees',
            'hotelier'               => 'hotels',
            'hotelso'                => 'hotels',
            'client'                 => 'clients',
            'planner'                => 'planners',
            'guest'                  => 'guests',
            'independent-contractor' => 'independent-contractors',
        ];

        if (! isset($map[$role])) {
            return false;
        }

        return $map[$role];
    }
}

if (! function_exists('formatDateRange')) {

    /**
     * Format a date range (start and end dates) to be displayed
     * on buttons, labels etc.
     *
     * @param  \Carbon   $startDate
     * @param  \Carbon   $endDate
     *
     * @return string
     */
    function formatDateRange($startDate, $endDate)
    {
        if ($startDate->format('Y') !== $endDate->format('Y')) {
            return $startDate->format('M j, Y') . ' to ' . $endDate->format('M j, Y');
        }

        if ($startDate->format('m') !== $endDate->format('m')) {
            return $startDate->format('M j') . ' to ' . $endDate->format('M j') . ', ' . $startDate->format('Y');
        }

        return $startDate->format('M j') . '-' . $endDate->format('j') . ', ' . $startDate->format('Y');
    }
}

if (! function_exists('convertProposedChangeForDisplay')) {

    function convertProposedChangeForDisplay($proposed, $key)
    {
        $converted = $proposed;

        switch ($key) {
            case 'term_groups':
                $group = json_decode($proposed);
                $converted = $group->name;
                // Also append all terms in this group
                foreach ($group->terms as $term) {
                    $converted .= PHP_EOL . PHP_EOL . $term->description;
                }
                break;

            case (preg_match('/term_groups.id:\d*.terms/', $key) ? $key : !$key):
                $term = json_decode($proposed);
                if (isset($term->description)) {
                    $converted = $term->description;
                } else {
                    $converted = $term->title;
                }
                break;

            default:
                break;
        }

        return $converted;
    }
}
