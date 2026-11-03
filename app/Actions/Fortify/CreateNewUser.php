<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ], [
            'username.regex' => 'The username may only contain letters, numbers, underscores (_), and dots (.) with no spaces.',
            'username.max' => 'The username must not be greater than 14 characters.',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'username' => ! empty($input['username']) ? $input['username'] : null,
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
