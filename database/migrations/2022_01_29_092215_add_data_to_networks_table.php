<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('networks')->insert(['name' => 'BEP20']);
        DB::table('networks')->insert(['name' => 'ERC20']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('networks')->where('name', 'BEP20')->delete();
        DB::table('networks')->where('name', 'ERC20')->delete();
    }
}
