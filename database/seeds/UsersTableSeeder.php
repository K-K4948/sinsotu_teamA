<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = new User;
        $model->name = 'mg-komatsuk';
        $model->password = bcrypt('9705');
        $model->is_admin = true;
        $model->save();
        // $model = new User;
        // $model->name = 'mg-hisada';
        // $model->password = bcrypt('1234');
        // $model->is_admin = true;
        // $model->save();
        // $model = new User;
        // $model->name = 'mg-motoyama';
        // $model->password = bcrypt('1234');
        // $model->is_admin = true;
        // $model->save();
    }
}
