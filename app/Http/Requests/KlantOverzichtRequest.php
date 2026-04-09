<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KlantOverzichtRequest extends FormRequest
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
            'zoekterm' => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN\s\.\,\-\'\@\(\)]*$/u'],
            'aantal_rijen' => ['nullable', 'integer', 'min:1', 'max:25'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'zoekterm.regex' => 'Zoekterm bevat ongeldige tekens.',
            'zoekterm.max' => 'Zoekterm mag maximaal 150 tekens bevatten.',
            'aantal_rijen.integer' => 'Aantal rijen moet een geheel getal zijn.',
            'aantal_rijen.min' => 'Aantal rijen moet minimaal 1 zijn.',
            'aantal_rijen.max' => 'Aantal rijen mag maximaal 25 zijn.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('zoekterm')) {
            return;
        }

        $this->merge([
            'zoekterm' => trim((string) $this->input('zoekterm')),
        ]);
    }
}
