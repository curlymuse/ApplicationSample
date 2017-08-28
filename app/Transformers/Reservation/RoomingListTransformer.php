<?php

namespace App\Transformers\Reservation;

use App\Transformers\Transformer;
use Illuminate\Support\Collection;

class RoomingListTransformer extends Transformer
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
        $roomSet = $object->roomSets()->first();

        $base = (object)collect($object)->only([
            'id',
            'status',
            'confirmation_number',
            'cancellation_number',
            'guest_name',
            'guest_email',
            'guest_phone',
            'guest_address',
            'guest_city',
            'guest_state',
            'guest_zip',
            'guest_country',
            'guest_special_requests',
            'guest_notes_to_hotel',
        ])->merge([
            'check_in_date'     => $object->roomSets()->pluck('reservation_date')->min()->format('Y-m-d'),
            'check_out_date'     => $object->roomSets()->pluck('reservation_date')->max()->format('Y-m-d'),
            'room_type_name'    => $roomSet->name,
            'room_type_description'    => $roomSet->description,
            'rate'  => $roomSet->rate,
            'secondary_guest_names' => $object->guests->implode('name', ', '),
        ])->toArray();

        $primaryGuest = $object->guests()->where('is_primary', true)->first();
        if ($primaryGuest) {
            $base = (object)collect($base)->merge([
                'guest_name'    => $primaryGuest->name,
                'guest_email'    => $primaryGuest->email,
                'guest_phone'    => $primaryGuest->phone,
                'guest_address'    => $primaryGuest->address,
                'guest_city'    => $primaryGuest->city,
                'guest_state'    => $primaryGuest->state,
                'guest_zip'    => $primaryGuest->zip,
                'guest_special_requests'    => $primaryGuest->special_requests,
                'guest_notes_to_hotel'    => $primaryGuest->notes_to_hotel,
            ])->toArray();
        }

        return $base;
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

        $i = 0;
        foreach ($objects as $object) {
            $arrayified = (array)$this->transform($object);

            //  First row is the column names
            if ($i++ == 0) {
                $columnHeadings = collect(array_keys($arrayified))->map(function($item){
                    return ucwords(str_replace('_', ' ', $item));
                })->toArray();

                $rows[] = implode(',', $columnHeadings);
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