<?php
use App\Role;
use App\User;
use App\Permission;
use Illuminate\Database\Seeder;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provider = Role::where('slug','provider')->first();
        $customer = Role::where('slug', 'customer')->first();
        $createTasks = Permission::where('slug','create-tasks')->first();
        $manageUsers = Permission::where('slug','manage-users')->first();

        $user1              = new User();
        $user1->name        = 'Jhon Deo';
        $user1->email       = 'jhon@deo.com';
        $user1->password    = bcrypt('secret');
        $user1->phone       = '8010915556';
        $user1->latitude    = 28.663870;
        $user1->longitude   = 77.235161;
        $user1->created_at  = date('Y-m-d H:i:s');
        $user1->updated_at  = date('Y-m-d H:i:s');
        $user1->save();
        $user1->roles()->attach($developer);
        $user1->permissions()->attach($createTasks);


        $user2 = new User();
        $user2->name = 'Mike Thomas';
        $user2->email = 'mike@thomas.com';
        $user2->password    = bcrypt('secret');
        $user2->phone       = '8010915556';
        $user2->latitude    = 28.663870;
        $user2->longitude   = 77.235161;
        $user2->created_at  = date('Y-m-d H:i:s');
        $user2->updated_at  = date('Y-m-d H:i:s');
        $user2->save();
        $user2->roles()->attach($manager);
        $user2->permissions()->attach($manageUsers);
    }
}
