<?php

namespace App\Http\Requests\LicenseeQuestion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenseeQuestionRequest extends FormRequest
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
            'question'      => 'string|required',
        ];
    }
}
