<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('admin:create {email} {name}')]
#[Description('Create or update an administrator account')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->argument('name');
        $password = $this->secret('Password');

        if (! is_string($password) || $password === '') {
            $this->components->error('A password is required.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->is_admin = true;
        $user->save();

        $this->components->info("Administrator account ready for {$email}.");

        return self::SUCCESS;
    }
}
