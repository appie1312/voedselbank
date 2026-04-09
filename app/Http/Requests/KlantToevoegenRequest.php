<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KlantToevoegenRequest extends FormRequest
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
            'gezinsnaam' => ['required', 'string', 'max:100', 'regex:/^[\pL\pN\s\.\,\-\'\(\)]*$/u'],
            'adres' => ['required', 'string', 'max:255'],
            'telefoonnummer' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]*$/'],
            'emailadres' => ['nullable', 'email:rfc,dns', 'max:150'],
            'aantal_volwassenen' => ['required', 'integer', 'min:0', 'max:20'],
            'aantal_kinderen' => ['required', 'integer', 'min:0', 'max:20'],
            'aantal_babys' => ['required', 'integer', 'min:0', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gezinsnaam.required' => 'Gezinsnaam is verplicht.',
            'gezinsnaam.regex' => 'Gezinsnaam bevat ongeldige tekens.',
            'adres.required' => 'Adres is verplicht.',
            'telefoonnummer.required' => 'Telefoonnummer is verplicht.',
            'telefoonnummer.regex' => 'Telefoonnummer bevat ongeldige tekens.',
            'emailadres.email' => 'E-mailadres is ongeldig.',
            'aantal_volwassenen.required' => 'Aantal volwassenen is verplicht.',
            'aantal_kinderen.required' => 'Aantal kinderen is verplicht.',
            'aantal_babys.required' => 'Aantal baby\'s is verplicht.',
            'aantal_volwassenen.min' => 'Aantal volwassenen kan niet negatief zijn.',
            'aantal_kinderen.min' => 'Aantal kinderen kan niet negatief zijn.',
            'aantal_babys.min' => 'Aantal baby\'s kan niet negatief zijn.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'gezinsnaam' => trim((string) $this->input('gezinsnaam')),
            'adres' => trim((string) $this->input('adres')),
            'telefoonnummer' => trim((string) $this->input('telefoonnummer')),
            'emailadres' => trim((string) $this->input('emailadres')),
        ]);
    }
}
