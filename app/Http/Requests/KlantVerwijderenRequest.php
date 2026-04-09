<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KlantVerwijderenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDirectie() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'klant_id' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'klant_id' => $this->route('klantId'),
        ]);
    }
}
