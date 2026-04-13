<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class DemandesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('demandes_conge')
            ->join('users', 'demandes_conge.user_id', '=', 'users.id')
            ->join('directions', 'users.direction_id', '=', 'directions.id')
            ->select(
                'users.nom',
                'users.prenom',
                'users.identifiant',
                'users.sexe',
                'directions.nom as direction',
                'demandes_conge.type_conge_id',
                'demandes_conge.date_debut',
                'demandes_conge.date_fin',
                'demandes_conge.statut',
                'demandes_conge.created_at'
            )
            ->orderBy('demandes_conge.created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Identifiant',
            'Sexe',
            'Direction',
            'Type de congé',
            'Date début',
            'Date fin',
            'Statut',
            'Date de soumission'
        ];
    }
}
