<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activity_types = [
            'education',
            'recreational',
            'social',
            'diy',
            'charity',
            'cooking',
            'relaxation',
            'music',
            'busywork'
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('activity_types')->truncate();

        foreach ($activity_types as $activity_type)
        {
            ActivityType::create([
                'name'  => $activity_type
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
