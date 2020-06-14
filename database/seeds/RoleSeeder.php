<?php
use App\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = new Role();
        $manager->name = 'customer';
        $manager->slug = 'customer';
        $manager->save();

        $developer = new Role();
        $developer->name = 'provider';
        $developer->slug = 'provider';
        $developer->save();
    }
}
