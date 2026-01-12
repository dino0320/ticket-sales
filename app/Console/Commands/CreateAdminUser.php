<?php

namespace App\Console\Commands;

use App\Models\Admin\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user if no admin users exist.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (AdminUser::count() > 0) {
            $this->info('An admin user already exists.');
            return 0;
        }

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if ($email === null || $password === null) {
            $this->error('ADMIN_EMAIL or ADMIN_PASSWORD is not set');
            return 1;
        }

        AdminUser::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('An admin user has been created successfully.');
        return 0;
    }
}
