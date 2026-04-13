<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('demandes_conge', function (Blueprint $table) {
            // 📎 Pièce jointe (certificat de grossesse si maternité)
            $table->string('piece_jointe')->nullable()->after('motif');

            // 📄 Certificat de reprise de service
            $table->string('certificat_reprise')->nullable()->after('piece_jointe');
        });
    }

    public function down(): void
    {
        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->dropColumn(['piece_jointe', 'certificat_reprise']);
        });
    }
};
