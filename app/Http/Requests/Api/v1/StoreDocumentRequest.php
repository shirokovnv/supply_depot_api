<?php

namespace App\Http\Requests\Api\v1;

use App\Enums\DocumentType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
            'type' => ['required', Rule::enum(DocumentType::class)],
            'performed_at' => 'required|date_format:Y-m-d H:i:s',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.value' => 'required|integer|min:1',
            'items.*.cost' => 'required_if:type,income|integer',
        ];
    }

    public function getType(): DocumentType
    {
        return DocumentType::from($this->get('type'));
    }

    /**
     * @return array<string, mixed>
     */
    public function getItems(): array
    {
        return $this->get('items');
    }

    public function getPerformedAt(): Carbon
    {
        return Carbon::parse($this->get('performed_at'));
    }
}
