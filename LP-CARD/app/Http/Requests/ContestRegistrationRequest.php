<?php

namespace App\Http\Requests;

use App\Models\Participant;
use Illuminate\Foundation\Http\FormRequest;

class ContestRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!Participant::canRegister($value)) {
                        $fail('Acest email s-a înscris deja în această săptămână!');
                    }
                }
            ],
            'phone' => 'required|string|max:20',
            'creative_answer' => 'required|string|min:10|max:1000',
            'newsletter_subscription' => 'boolean',
            'terms_accepted' => 'required|accepted'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Numele este obligatoriu.',
            'last_name.required' => 'Prenumele este obligatoriu.',
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să aibă un format valid.',
            'phone.required' => 'Telefonul este obligatoriu.',
            'creative_answer.required' => 'Răspunsul la întrebarea creativă este obligatoriu.',
            'creative_answer.min' => 'Răspunsul trebuie să aibă cel puțin 10 caractere.',
            'terms_accepted.required' => 'Trebuie să accepți regulamentul.',
            'terms_accepted.accepted' => 'Trebuie să accepți regulamentul.',
        ];
    }
}
