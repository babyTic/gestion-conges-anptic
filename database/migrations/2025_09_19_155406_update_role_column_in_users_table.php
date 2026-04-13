<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Supprimer l’ancienne contrainte si elle existe
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // Modifier la colonne en VARCHAR
        DB::statement("ALTER TABLE users 
            ALTER COLUMN role DROP DEFAULT,
            ALTER COLUMN role TYPE VARCHAR(50) USING role::VARCHAR");

        // Définir une nouvelle valeur par défaut
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'agent'");

        // Ajouter une contrainte de vérification (CHECK)
        DB::statement("
            ALTER TABLE users 
            ADD CONSTRAINT users_role_check 
            CHECK (role IN ('agent', 'responsable', 'rh', 'dg', 'admin'))
        ");
    }

    public function down()
    {
        // ⚠️ Revenir à l'ancien enum si besoin
        Schema::table('users', function (Blueprint $table) {
            // Exemple rollback si ton enum original était sans 'dg'
            // $table->enum('role', ['agent', 'responsable', 'rh', 'admin'])->default('agent')->change();
        });
    }
};
