<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ispecia\Installer\Database\Seeders\DatabaseSeeder as IspeciaDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(IspeciaDatabaseSeeder::class);
    }
}
