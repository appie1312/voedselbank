<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllergieOverzichtRequest extends FormRequest
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
            'klant_id' => ['nullable', 'integer', 'min:1'],
            'zoekterm' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN\s\.\,\-\'\(\)]*$/u'],
            'aantal_rijen' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'klant_id.integer' => 'Klant-id moet een geheel getal zijn.',
            'klant_id.min' => 'Klant-id moet minimaal 1 zijn.',
            'zoekterm.regex' => 'Zoekterm bevat ongeldige tekens.',
            'zoekterm.max' => 'Zoekterm mag maximaal 100 tekens bevatten.',
            'aantal_rijen.integer' => 'Aantal rijen moet een geheel getal zijn.',
            'aantal_rijen.min' => 'Aantal rijen moet minimaal 1 zijn.',
            'aantal_rijen.max' => 'Aantal rijen mag maximaal 50 zijn.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'klant_id' => $this->has('klant_id') ? $this->input('klant_id') : null,
            'zoekterm' => trim((string) $this->input('zoekterm', '')),
            'aantal_rijen' => $this->input('aantal_rijen', 10),
        ]);
    }
}
