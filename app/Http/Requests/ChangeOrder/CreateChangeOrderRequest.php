<?php

namespace App\Http\Requests\ChangeOrder;

use Illuminate\Foundation\Http\FormRequest;

class CreateChangeOrderRequest extends FormRequest
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
            'changes'            => 'array|required',
            'labels'             => 'array|required',
            'add_attachments'    => 'array',
            'remove_attachments' => 'array',
        ];
    }

    protected function validationData()
    {
        $changes = json_decode($this->get('changes'));
        $reservationMethodIds = isset($changes->reservation_methods) ? (array)$changes->reservation_methods : [];
        $paymentMethodIds = isset($changes->payment_methods) ? (array)$changes->payment_methods : [];

        $changes = to_array_deep($changes);
        $changes['reservation_methods'] = $reservationMethodIds;
        $changes['payment_methods'] = $paymentMethodIds;

        $this->replace([
            'reason'            => $this->get('reason'),
            'changes'            => $changes,
            'labels'             => to_array_deep(json_decode($this->get('labels'))),
            'remove_attachments' => to_array_deep(json_decode($this->get('remove_attachments'))),
            'add_attachments'    => [
                'files'      => $this->file('add_attachments.files'),
                'categories' => to_array_deep(json_decode($this->get('add_attachments')['categories']))
            ]
        ]);

        return $this->all();
    }
}
