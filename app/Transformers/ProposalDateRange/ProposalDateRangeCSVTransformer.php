<?php

namespace App\Transformers\ProposalDateRange;

use App\Transformers\Transformer;
use Illuminate\Support\Collection;

class ProposalDateRangeCSVTransformer extends Transformer
{
    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        $return = [
            'Hotel' => $object->proposal->hotel->name,
            'Brand' => $object->proposal->hotel->brand->name,
            // 'Travelocity Rating'    => $object->proposal->hotel->travelocity_rating,
            'Tax Rate'  => $object->proposal->tax_rate,
            'Comments/Notes'  => '',
            'Cutoff Date'   => $object->proposal->honor_bid_until->format('Y-m-d'),
            'Deposit Terms' => $object->proposal->deposit_policy,
            'Contact'   => $object->userWhoSubmitted->name,
            'Contact Email'   => $object->userWhoSubmitted->email,
        ];

        $this->injectRoomStats($object, $return);
        $this->injectSpaceRequestData($object, $return);

        return $return;
    }

    /**
     * @param $object
     * @param $return
     */
    private function injectSpaceRequestData($object, &$return)
    {
        $types = [
            'Meeting'   => [
                'label' => 'Meeting Spaces',
                'key'   => 'meeting_spaces',
            ],
            'Food & Beverage'   => [
                'label' => 'Food and Beverage',
                'key' => 'food_and_beverage',
            ],
        ];

        foreach ($types as $type => $info) {
            $spacesOffered = collect(json_decode($object->{$info['key']}))->pluck('id');
            $spacesNeeded = $object->eventDateRange->spaceRequests->where('type', $type)->pluck('id');

            if (count($spacesOffered) == 0) {
                $return[$info['label']] = 'None Provided';
            } else if ($spacesOffered == $spacesNeeded) {
                $return[$info['label']] = 'All Provided';
            } else {
                $return[$info['label']] = 'Some Provided';
            }
        }
    }

    /**
     * @param $object
     * @param $return
     */
    private function injectRoomStats($object, &$return)
    {
        $roomData = [];
        $roomNames = [];
        $rooms = json_decode($object->rooms);
        foreach ($object->eventDateRange->roomRequestDates as $roomRequestDate) {
            foreach ($rooms as $room) {
                $roomNames[$room->name] = isset($room->room_name) ? $room->room_name : '';
                if ($room->name == $roomRequestDate->room_type_name) {
                    if (! isset($roomData[$room->name])) {
                        $roomData[$room->name] = [];
                    }
                    $roomData[$room->name][] = $room->rate;
                }
            }
        }

        foreach ($roomData as $roomType => $rates) {
            $return[sprintf('Room:%s - Range', $roomType)] = sprintf('%d - %d', min($rates), max($rates));
            $return[sprintf('Room:%s - Average', $roomType)] = (int)(array_sum($rates) / count($rates));
            $return[sprintf('Room:%s - Name', $roomType)] = $roomNames[$roomType];
        }
    }

    /**
     * Transform to CSV
     *
     * @param Collection $objects
     */
    public function transformToCSV(Collection $objects)
    {
        $rows = [];

        if (count($objects) == 0) {
            return '';
        }

        //  Add the event name first
        $rows[] = $objects->first()->proposal->proposalRequest->event->name;

        $i = 0;
        foreach ($objects as $object) {
            $arrayified = $this->transform($object);

            //  First row is the column names
            if ($i++ == 0) {
                $rows[] = implode(',', array_keys($arrayified));
            }

            //  Escape any internal commas
            $escaped = collect(array_values($arrayified))->map(function($value) {
               return str_replace(',', '\,', $value);
            })->toArray();
            $rows[] = implode(',', $escaped);
        }

        return implode("\r\n", $rows);
    }
}
