<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class UnitStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Status::query()->create(['name' => 'Available']);
    }
}
