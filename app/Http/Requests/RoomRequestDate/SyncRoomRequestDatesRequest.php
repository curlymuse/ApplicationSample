<?php

namespace App\Http\Requests\RoomRequestDate;

use Illuminate\Foundation\Http\FormRequest;

class SyncRoomRequestDatesRequest extends FormRequest
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
            'room_request_dates'    => 'array',
        ];
    }
}
