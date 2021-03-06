<?php

namespace App\Actions\Fortify;

use App\Models\Nutricionista;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'nome' => ['required', 'string', 'min:5', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:5', 'max:255', 'unique:users'],
            'cpf' => ['required', 'string', 'cpf', 'unique:users'],
            'telefone_1' => ['required', 'string', 'celular_com_ddd'],
            'telefone_2' => ['string', 'celular_com_ddd'],
            'crn' => ['required', 'string', 'min:8', 'max:45', 'unique:nutricionistas'],
            'uf' => ['required', 'string', 'uf'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $usuario = User::create([
            'nome' => $input['nome'],
            'email' => $input['email'],
            'cpf' => $input['cpf'],
            'telefone_1' => $input['telefone_1'],
            'telefone_2' => $input['telefone_2'] ?? null,
            'tipo_usuario' => 2,
            'password' => Hash::make($input['password']),
        ]);

        Nutricionista::create([
            'crn' => $input['crn'],
            'uf' => $input['uf'],
            'user_id' => $usuario->id,
        ]);

        return $usuario;
    }
}
