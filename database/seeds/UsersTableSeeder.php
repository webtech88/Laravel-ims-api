<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permanent Users
        DB::table('users')->insert([
            //'id'            => 1,
            'name'     => 'Anton',
            'email'          => 'anton@kwtdesign.co.uk',
            'password'       => Hash::make('qwert22'),
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()
        ]);
    }
}
