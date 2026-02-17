<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChangeRequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_current' => ['nullable', 'string', 'max:255'],
            'title_proposed' => ['nullable', 'string', 'max:255'],
            'description_current' => ['nullable', 'string', 'max:5000'],
            'description_proposed' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $proposed = $this->input('title_proposed') ?? '';
            $desc = $this->input('description_proposed') ?? '';
            if (trim($proposed) === '' && trim($desc) === '') {
                $validator->errors()->add(
                    'title_proposed',
                    'At least one proposed value (title or description) is required.'
                );
            }
        });
    }
}
