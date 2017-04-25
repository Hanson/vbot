<?php

namespace Hanson\Vbot\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserMigration extends Migration
{
    public function up()
    {
        $this->schema->create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('user');
    }
}
