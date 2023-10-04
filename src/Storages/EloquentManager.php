<?php 
namespace Erahma\FutureFramework\Storages;

use Erahma\FutureFramework\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

final class EloquentManager 
{
    public static function init($driver, $host, $database, $username, $password)  {

        $capsule = new Capsule;
        $capsule->addConnection([
            "driver" => $driver,
            "host" => $host,
            "database" => $database,
            "username" => $username,
            "password" => $password,
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        EloquentManager::initTable();
        EloquentManager::initMessageTable();
        EloquentManager::seeder();

    }

    public static function initTable() {
        /* Capsule::schema()->dropIfExists('users'); */
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('user_image')->nullable();
                $table->string('api_key')->nullable()->unique();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public static function initMessageTable() {
        /* Capsule::schema()->dropIfExists('messages'); */
        if (!Capsule::schema()->hasTable('messages')) {
            Capsule::schema()->create('messages', function ($table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('email');
                $table->string('message');
                $table->string('is_read');
                $table->timestamps();
            });
        }
    }

    public static function seeder() {
        if (is_null(User::first())) {
            $user = User::create([
                'name'=>  'Achmadi',
                'email'=>  'test@gmail.com',
                'password'=>  'password',
                'user_image'=>  'Achmadi',
                'api_key'=>  'api_key',
            ]);
            $user ->messages()->create([
                'user_id'=> '',
                'email'=> $user ->email,
                'message'=> 'TEst Messate',
                'is_read'=> 0,
            ]);
        }
    }
}
