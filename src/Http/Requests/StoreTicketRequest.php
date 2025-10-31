<?php

namespace Bithoven\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-tickets');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'category_id' => ['nullable', 'exists:ticket_categories,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'attachments.*' => [
                'nullable',
                'file',
                'max:' . config('tickets.uploads.max_size'),
                'mimes:' . implode(',', config('tickets.uploads.allowed_types'))
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Please enter a subject for the ticket',
            'description.required' => 'Please provide a description of the issue',
            'priority.required' => 'Please select a priority level',
            'attachments.*.max' => 'File size must not exceed ' . (config('tickets.uploads.max_size') / 1024) . 'MB',
        ];
    }
}
