<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('demandes_conge', function (Blueprint $table) {
        $table->foreignId('interimaire_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('demandes_conge', function (Blueprint $table) {
        $table->dropForeign(['interimaire_id']);
        $table->dropColumn('interimaire_id');
    });
}

};
