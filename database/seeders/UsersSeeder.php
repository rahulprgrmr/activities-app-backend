<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@activitiesapp.com',
            'password'          => Hash::make("12345678"),
            'remember_token'    => Str::random(10),
            'role_id'           => config('constants.USER_ROLES.ADMIN')
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
