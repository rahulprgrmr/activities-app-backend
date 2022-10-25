<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin',
            'User'
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();

        foreach ($roles as $role)
        {
            Role::create([
                'name'  => $role
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
