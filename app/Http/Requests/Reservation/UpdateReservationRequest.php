<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status'            => 'string|required',
            'confirmation_number' => 'string',
            'cancellation_number' => 'string',
            'guest_attributes'    => 'array|required',
            'guests'            => 'array',
            'room_set_ids'      => 'array',
        ];
    }
}
