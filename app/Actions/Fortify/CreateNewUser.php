<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Team;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'email' => $input['email'],
            ]), function (User $user) {
                $this->createTeam($user);
                $user->notify(new WelcomeNotification);
            });
        });
    }

    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => 'Personal',
            'personal_team' => true,
        ]));
    }
}
