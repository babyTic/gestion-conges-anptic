<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->string('autorisation_path')->nullable()->after('piece_jointe');
            $table->string('autorisation_signee_path')->nullable()->after('autorisation_path');
            $table->string('certificat_path')->nullable()->after('autorisation_signee_path');
        });

        // ✅ Mise à jour de la contrainte CHECK (PostgreSQL)
        DB::statement("ALTER TABLE demandes_conge DROP CONSTRAINT IF EXISTS demandes_conge_statut_check");
        DB::statement("
            ALTER TABLE demandes_conge
            ADD CONSTRAINT demandes_conge_statut_check
            CHECK (statut IN (
                'soumis',
                'approuve_responsable',
                'approuve_rh',
                'en_attente_signature_dg',
                'approuve_dg',
                'rejete',
                'termine'
            ))
        ");
    }

    public function down()
    {
        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->dropColumn(['autorisation_path', 'autorisation_signee_path', 'certificat_path']);
        });

        // ⚠️ Optionnel : restaurer l'ancien check si nécessaire
    }
};
