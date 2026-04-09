<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllergieToevoegenRequest extends FormRequest
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
            'klant_id' => ['required', 'integer', 'min:1', 'exists:klanten,id'],
            'beschrijving' => ['required', 'string', 'max:100', 'regex:/^[\pL\pN\s\.\,\-\'\(\)]*$/u'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'klant_id.required' => 'Klant-id is verplicht.',
            'klant_id.integer' => 'Klant-id moet een geheel getal zijn.',
            'klant_id.min' => 'Klant-id moet minimaal 1 zijn.',
            'klant_id.exists' => 'De geselecteerde klant bestaat niet.',
            'beschrijving.required' => 'Beschrijving is verplicht.',
            'beschrijving.max' => 'Beschrijving mag maximaal 100 tekens bevatten.',
            'beschrijving.regex' => 'Beschrijving bevat ongeldige tekens.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'klant_id' => $this->input('klant_id'),
            'beschrijving' => trim((string) $this->input('beschrijving')),
        ]);
    }
}
