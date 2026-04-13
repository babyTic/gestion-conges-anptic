<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣ Supprimer l’ancienne contrainte
        DB::statement("ALTER TABLE demandes_conge DROP CONSTRAINT IF EXISTS demandes_conge_statut_check");

        // 2️⃣ Convertir en VARCHAR(50) sans contrainte
        DB::statement("ALTER TABLE demandes_conge 
            ALTER COLUMN statut DROP DEFAULT,
            ALTER COLUMN statut TYPE VARCHAR(50) USING statut::VARCHAR");

        // 3️⃣ Mettre à jour les anciennes valeurs → plus d’erreur car plus de contrainte
        DB::table('demandes_conge')
            ->where('statut', 'attente_responsable')
            ->update(['statut' => 'soumis']);

        DB::table('demandes_conge')
            ->where('statut', 'attente_rh')
            ->update(['statut' => 'approuve_responsable']); // ou autre selon workflow

        DB::table('demandes_conge')
            ->where('statut', 'approuvé')
            ->update(['statut' => 'approuve_rh']); // ou 'approuve_dg'

        DB::table('demandes_conge')
            ->where('statut', 'rejeté')
            ->update(['statut' => 'rejete']);

        // 4️⃣ Définir un nouveau défaut
        DB::statement("ALTER TABLE demandes_conge ALTER COLUMN statut SET DEFAULT 'soumis'");

        // 5️⃣ Recréer la contrainte CHECK avec les nouveaux statuts
        DB::statement("
            ALTER TABLE demandes_conge 
            ADD CONSTRAINT demandes_conge_statut_check 
            CHECK (statut IN ('soumis', 'approuve_responsable', 'approuve_rh', 'approuve_dg', 'rejete'))
        ");
    }

    public function down()
    {
        // rollback → remettre l’ancien enum
        Schema::table('demandes_conge', function (Blueprint $table) {
            // $table->enum('statut', ['attente_responsable','attente_rh','approuvé','rejeté'])->default('attente_responsable')->change();
        });
    }
};
