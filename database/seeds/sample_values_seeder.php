<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class sample_values_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $base_controller = new Controller;

        $store_1 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE1")),
            "name" => "Appsthing POS Store 1",
            "tax_number" => "100000000000",
            "address" => $faker->address,
            "pincode" => "100111",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "SMALL",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $store_2 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE2")),
            "name" => "Appsthing POS Store 2",
            "tax_number" => "100000000001",
            "address" => $faker->address,
            "pincode" => "100222",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "A4",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $manager_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '100',
            'label' => 'Manager', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $cashier_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '101',
            'label' => 'Cashier', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("role_menus")->insert([
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 2,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 9,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 34,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 35,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'role_id' => $cashier_role_id,
                'menu_id' => 36,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        ]);

        DB::table("role_menus")->insert([
            ['role_id' => $manager_role_id,'menu_id' => '1','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '2','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '3','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '4','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '5','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '6','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '7','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '8','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '9','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '10','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '11','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '12','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '13','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '14','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '15','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '16','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '17','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '18','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '19','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '20','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '21','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '22','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '25','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '26','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '27','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '28','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '29','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '30','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '31','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '32','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '33','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '34','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '35','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '36','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '37','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '38','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '39','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '40','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '41','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '42','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '43','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '44','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '45','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '46','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '47','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '48','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '49','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '50','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '51','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '52','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '53','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '54','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '55','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '56','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '57','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '58','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '59','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '60','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '61','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '62','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '63','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '64','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '65','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '66','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '67','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '68','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '69','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '70','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '71','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['role_id' => $manager_role_id,'menu_id' => '72','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_role_id,'menu_id' => '73','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()]
        ]);

        $hashed_password = Hash::make("posuser");

        $manager_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "100",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => $faker->unique()->email,
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => 2, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $cashier_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "101",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => $faker->unique()->email,
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => 3, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("user_menus")->insert([
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 2,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 9,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 34,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 35,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ],
            [
                'user_id' => $cashier_user_id,
                'menu_id' => 36,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        ]);

        DB::table("user_menus")->insert([
            ['user_id' => $manager_user_id,'menu_id' => '1','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '2','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '3','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '4','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '5','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '6','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '7','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '8','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '9','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '10','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '11','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '12','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '13','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '14','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '15','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '16','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '17','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '18','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '19','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '20','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '21','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '22','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '25','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '26','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '27','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '28','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '29','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '30','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '31','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '32','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '33','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '34','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '35','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '36','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '37','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '38','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '39','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '40','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '41','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '42','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '43','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '44','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '45','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '46','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '47','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '48','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '49','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '50','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '51','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '52','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '53','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '54','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '55','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '56','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '57','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '58','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '59','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '60','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '61','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '62','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '63','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '64','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '65','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '66','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '67','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '68','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '69','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '70','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '71','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '72','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'menu_id' => '73','created_by' => '1','created_at' => NOW(),'updated_at' => NOW()]
        ]);

        DB::table("user_stores")->insert([
            ['user_id' => $manager_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()]
        ]);

        DB::table("user_stores")->insert(
            ['user_id' => $cashier_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()]
        );

        DB::table("payment_methods")->insert([
            "slack" => $base_controller->generate_slack("payment_methods"),
            "label" => Str::title("Cash"),
            "description" => "Cash Payment",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("payment_methods")->insert([
            "slack" => $base_controller->generate_slack("payment_methods"),
            "label" => Str::title("Card"),
            "description" => "Card Payment",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $supplier_1 = DB::table("suppliers")->insertGetId([
            "slack" => $base_controller->generate_slack("suppliers"),
            "store_id" => $store_1,
            "supplier_code" => "SUP1",
            "name" => "Appsthing Store 1 Supplier 1",
            "address" => $faker->address,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $supplier_2 = DB::table("suppliers")->insertGetId([
            "slack" => $base_controller->generate_slack("suppliers"),
            "store_id" => $store_1,
            "supplier_code" => "SUP2",
            "name" => "Appsthing Store 1 Supplier 2",
            "address" => $faker->address,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $laptops = DB::table("category")->insertGetId([
            "slack" => $base_controller->generate_slack("category"),
            "category_code" => "101",
            "store_id" => $store_1,
            "label" => "Laptops",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

       $mobiles = DB::table("category")->insertGetId([
            "slack" => $base_controller->generate_slack("category"),
            "category_code" => "102",
            "store_id" => $store_1,
            "label" => "Mobiles",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $tax_code  = DB::table("tax_codes")->insertGetId([
            "slack" => $base_controller->generate_slack("tax_codes"),
            "store_id" => $store_1,
            "label" =>  "Tax 7.5%",
            "tax_code" => "TAX75",
            "total_tax_percentage" => 7.5,
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("tax_code_type")->insert([
            "tax_code_id" => 1,
            "tax_type" => "GST",
            "tax_percentage" => 7.5,
            "created_at" => NOW(),
            "created_by" => 1,
        ]);

        $discount_code = DB::table("discount_codes")->insertGetId([
            "slack" => $base_controller->generate_slack("discount_codes"),
            "store_id" => $store_1,
            "label" => "Discount 10%",
            "discount_code" => "DISCOUNT10",
            "discount_percentage" => "10",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);
        
        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple iPhone 8 Plus (Silver, 64 GB)",
            "product_code" => "101",
            "description" => "",
            "category_id" => $mobiles,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 400,
            "sale_amount_excluding_tax" => 449,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple iPad Pro (2018) 64 GB 11 inch with Wi-Fi+4G (Silver)",
            "product_code" => "102",
            "description" => "",
            "category_id" => $mobiles,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1500,
            "sale_amount_excluding_tax" => 1699,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple iPhone XR (White, 64 GB)",
            "product_code" => "103",
            "description" => "",
            "category_id" => $mobiles,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "discount_code_id" => $discount_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 500,
            "sale_amount_excluding_tax" => 599,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple iPhone 11 (White, 64 GB)",
            "product_code" => "104",
            "description" => "",
            "category_id" => $mobiles,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "discount_code_id" => $discount_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 600,
            "sale_amount_excluding_tax" => 699,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple MacBook Pro Core i5 - (8 GB/256 GB SSD/Mac OS Mojave) MPXU2HN/A  (13.3 inch, Silver, 1.37 kg)",
            "product_code" => "105",
            "description" => "",
            "category_id" => $laptops,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "discount_code_id" => $discount_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1000,
            "sale_amount_excluding_tax" => 1299,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_1,
            "name" => "Apple MacBook Air Core i5 5th Gen - (8 GB/128 GB SSD/Mac OS Sierra) MQD32HN/A A1466  (13.3 inch, Silver, 1.35 kg)",
            "product_code" => "106",
            "description" => "",
            "category_id" => $laptops,
            "supplier_id" => $supplier_1,
            "tax_code_id" => $tax_code,
            "discount_code_id" => $discount_code,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1000,
            "sale_amount_excluding_tax" => 1099,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        //store 2

        $supplier_1_store_2 = DB::table("suppliers")->insertGetId([
            "slack" => $base_controller->generate_slack("suppliers"),
            "store_id" => $store_2,
            "supplier_code" => "SUP3",
            "name" => "Appsthing Store 2 Supplier 1",
            "address" => $faker->address,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $supplier_2_store_2 = DB::table("suppliers")->insertGetId([
            "slack" => $base_controller->generate_slack("suppliers"),
            "store_id" => $store_2,
            "supplier_code" => "SUP4",
            "name" => "Appsthing Store 2 Supplier 2",
            "address" => $faker->address,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $category_1_store_2 = DB::table("category")->insertGetId([
            "slack" => $base_controller->generate_slack("category"),
            "category_code" => "101",
            "store_id" => $store_2,
            "label" => "Laptops",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $category_2_store_2 = DB::table("category")->insertGetId([
            "slack" => $base_controller->generate_slack("category"),
            "category_code" => "102",
            "store_id" => $store_2,
            "label" => "Mobiles",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $tax_code_store_2 = DB::table("tax_codes")->insertGetId([
            "slack" => $base_controller->generate_slack("tax_codes"),
            "store_id" => $store_2,
            "label" =>  "Tax 7.5%",
            "tax_code" => "TAX75",
            "total_tax_percentage" => 7.5,
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("tax_code_type")->insert([
            "tax_code_id" => $tax_code_store_2,
            "tax_type" => "GST",
            "tax_percentage" => 7.5,
            "created_at" => NOW(),
            "created_by" => 1,
        ]);

        $discount_code_store_2 = DB::table("discount_codes")->insertGetId([
            "slack" => $base_controller->generate_slack("discount_codes"),
            "store_id" => $store_2,
            "label" => "Discount 10%",
            "discount_code" => "DISCOUNT10",
            "discount_percentage" => "10",
            "description" => "",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple iPhone 8 Plus (Silver, 64 GB)",
            "product_code" => "101",
            "description" => "",
            "category_id" => $category_2_store_2,
            "supplier_id" => $supplier_1_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 400,
            "sale_amount_excluding_tax" => 449,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple iPad Pro (2018) 64 GB 11 inch with Wi-Fi+4G (Silver)",
            "product_code" => "102",
            "description" => "",
            "category_id" => $category_2_store_2,
            "supplier_id" => $supplier_1_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1500,
            "sale_amount_excluding_tax" => 1699,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple iPhone XR (White, 64 GB)",
            "product_code" => "103",
            "description" => "",
            "category_id" => $category_2_store_2,
            "supplier_id" => $supplier_1_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 500,
            "sale_amount_excluding_tax" => 599,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple iPhone 11 (White, 64 GB)",
            "product_code" => "104",
            "description" => "",
            "category_id" => $category_2_store_2,
            "supplier_id" => $supplier_1_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 600,
            "sale_amount_excluding_tax" => 699,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple MacBook Pro Core i5 - (8 GB/256 GB SSD/Mac OS Mojave) MPXU2HN/A  (13.3 inch, Silver, 1.37 kg)",
            "product_code" => "105",
            "description" => "",
            "category_id" => $category_1_store_2,
            "supplier_id" => $supplier_1_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1000,
            "sale_amount_excluding_tax" => 1299,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("products")->insert([
            "slack" => $base_controller->generate_slack("products"),
            "store_id" => $store_2,
            "name" => "Apple MacBook Air Core i5 5th Gen - (8 GB/128 GB SSD/Mac OS Sierra) MQD32HN/A A1466  (13.3 inch, Silver, 1.35 kg)",
            "product_code" => "106",
            "description" => "",
            "category_id" => $category_1_store_2,
            "supplier_id" => $supplier_2_store_2,
            "tax_code_id" => $tax_code_store_2,
            "quantity" => 1000,
            "purchase_amount_excluding_tax" => 1000,
            "sale_amount_excluding_tax" => 1099,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);
    }
}
