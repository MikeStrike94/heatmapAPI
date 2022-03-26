<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $types = array(
            'product',
            'category',
            'static-page',
            'checkout',
            'homepage'
        );

        foreach($types as $type) {
            Type::firstOrCreate(['name' => $type]);
        }
    }
}
