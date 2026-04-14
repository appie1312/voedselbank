<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllergieVerwijderenRequest extends FormRequest
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
            'allergie_id' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'allergie_id' => $this->route('allergieId'),
        ]);
    }
}
