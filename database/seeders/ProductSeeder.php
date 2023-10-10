<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') != 'production') {
            Product::factory()->sold()->create();
            Product::factory()->purchased()->create();
            Product::factory()->tracked()->create();
            Product::factory()->sold()->purchased()->create();
            Contact::factory()->create(3);
        }
    }
}
