<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('types_conge', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Ex: "Congé annuel", "Maladie"
            $table->integer('jours_alloues'); // Nombre de jours autorisés
            $table->boolean('est_payee')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('types_conge');
    }
};
