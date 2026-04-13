<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_conge_id')->constrained('demandes_conge')->onDelete('cascade');
            $table->string('chemin_fichier'); // Chemin vers le fichier stocké
            $table->string('type'); // Ex: "justificatif", "autorisation"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
