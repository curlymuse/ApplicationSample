<?php

namespace App\Mail\Traits;

trait NeedsProposalRequestStats
{
    /**
     * @var \App\Models\ProposalRequest
     */
    protected $request;

    /**
     * @var \App\Models\User
     */
    public $recipient;

    /**
     * Get integer expressing total cost of food and beverage
     *
     * @return int
     */
    protected function getFoodAndBeverage()
    {
        $foodTotals = collect([]);

        foreach ($this->request->event->dateRanges as $range) {

            $thisFoodTotal = 0;
            $requests = $range->spaceRequests()->whereType('Food & Beverage')->get();
            foreach ($requests as $request) {

                if ($request->budget_units == 'per person') {
                    $thisFoodTotal += ($request->budget * $request->attendees);
                    continue;
                }

                $thisFoodTotal += $request->budget;
            }

            $foodTotals->push($thisFoodTotal);
        }

        if (count($this->request->event->dateRanges) == 1) {
            return (int)$foodTotals->first();
        }

        return sprintf(
            '%d avg',
            (int)$foodTotals->avg()
        );
    }

    /**
     * Get string expressing number of meeting attendees
     *
     * @return int
     */
    protected function getMeetingSpaceAttendees()
    {
        $spaceTotals = collect([]);

        foreach ($this->request->event->dateRanges as $range) {
            $spaceTotals->push(
                $range
                    ->spaceRequests()
                    ->whereType('Meeting')
                    ->avg('attendees')
            );
        }

        if (count($this->request->event->dateRanges) == 1) {
            return (int)$spaceTotals->first();
        }

        return sprintf(
            'approx %d',
            (int)$spaceTotals->avg()
        );
    }

    /**
     * Get string expressing the number of room nights
     *
     * @return int
     */
    protected function getRoomNights()
    {
        $roomTotals = collect([]);
        foreach ($this->request->event->dateRanges as $range) {
            $roomTotals->push($range->roomRequestDates->sum('rooms_requested'));
        }

        if (count($this->request->event->dateRanges) == 1) {
            return $roomTotals->first();
        }

        return sprintf(
            'approx %d',
            $roomTotals->avg()
        );
    }

    /**
     * Return location string
     *
     * @return string
     */
    protected function getLocationString()
    {
        if (count($this->request->eventLocations) > 1) {
            return 'Multiple Locations';
        }

        if (count($this->request->eventLocations) == 0) {
            return 'No Locations';
        }

        return $this->request->eventLocations[0]->present()->city;
    }

    /**
     * If there is one date range, return a string expressing this. If there are multiple
     * date ranges, return a string literal
     *
     * @return string
     */
    protected function getDateString()
    {
        if (count($this->request->event->dateRanges) > 1) {
            return 'Various';
        }

        if (count($this->request->event->dateRanges) == 0) {
            return 'None';
        }

        return $this->request->event->dateRanges[0]->present()->range;
    }

    /**
     * Get personalized view link for this proposal request, this user
     *
     * @return string
     */
    protected function getViewLink()
    {
        $params = collect($this->getHashParams())
            ->merge([
                'requestId' => $this->request->id,
                'action'    => 'detail',
            ])->toArray();

        return route('hotels.proposal-requests.gateway', $params);
    }

    /**
     * Get personalized decline link for this proposal request, this user
     *
     * @return string
     */
    protected function getDeclineLink()
    {
        $params = collect($this->getHashParams())
            ->merge([
                'requestId'     => $this->request->id,
                'action'    => 'decline',
            ])->toArray();

        return route('hotels.proposal-requests.gateway', $params);
    }

    /**
     * Get an array containing the user ID and hash, formatted for query string use
     *
     * @return array
     */
    private function getHashParams()
    {
        $userKey = config('resbeat.urls.query-string-user-key');
        $hashKey = config('resbeat.urls.query-string-hash-key');

        $data = [];
        $data[$userKey] = $this->recipient->id;
        $data[$hashKey] = $this->recipient
            ->requestHotels()
            ->whereProposalRequestId($this->request->id)
            ->whereHotelId($this->hotel->id)
            ->first()
            ->pivot
            ->hash;

        return $data;
    }
}
