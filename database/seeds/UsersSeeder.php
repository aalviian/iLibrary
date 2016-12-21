<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Membuat Role Admin
        $adminRole = new Role();
        $adminRole -> name = "admin";
        $adminRole -> display_name = "Admin";
        $adminRole -> save();

        //Membuat Role Member
        $memberRole = new Role();
        $memberRole -> name = "member";
        $memberRole -> display_name = "Member";
        $memberRole -> save();

        //Membuat User Admin
        $admin = new User();
        $admin -> name = "Alvian";
        $admin -> email = "aalviian@gmail.com";
        $admin -> password = bcrypt('admin');
        $admin -> save();
        $admin -> attachRole($adminRole);

        //Membuat User Member
        $admin = new User();
        $admin -> name = "Kamal";
        $admin -> email = "imkamalh@gmail.com";
        $admin -> password = bcrypt('member');
        $admin -> save();
        $admin -> attachRole($memberRole);

        $faker = Faker\Factory::create('id_ID');

        for($i = 0; $i < 10; $i++) {
            $user=User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('member')
            ]);
        }   
    }
}
