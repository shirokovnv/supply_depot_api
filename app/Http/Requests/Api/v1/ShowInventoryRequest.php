<?php

namespace App\Http\Requests\Api\v1;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ShowInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'performed_at' => 'required|date_format:Y-m-d'
        ];
    }

    /**
     * @return Carbon
     */
    public function getPerformedAt(): Carbon
    {
        return Carbon::parse($this->get('performed_at'));
    }
}
