<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Ajout du sexe dans la table users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('sexe', ['H', 'F'])->nullable()->after('prenom'); 
            // "H" = Homme, "F" = Femme (nullable pour ne pas casser les anciens enregistrements)
        });

        // ✅ Ajout du lieu dans la table demandes_conge
        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->string('lieu')->nullable()->after('date_fin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sexe');
        });

        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->dropColumn('lieu');
        });
    }
};
