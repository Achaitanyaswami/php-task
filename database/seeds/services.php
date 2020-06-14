<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class services extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            'title'				=> Str::random(6),
            'price'				=> rand(10,1000),
            'created_at'  		=> date('Y-m-d H:i:s'),
            'updated_at'       	=> date('Y-m-d H:i:s'),
        ]);
    }
}


