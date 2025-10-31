<?php

namespace Bithoven\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit-tickets');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'status' => ['sometimes', 'required', 'in:open,in_progress,pending,resolved,closed'],
            'category_id' => ['nullable', 'exists:ticket_categories,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
