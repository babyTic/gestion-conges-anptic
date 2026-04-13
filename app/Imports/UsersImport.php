<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Direction;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class UsersImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors, SkipsFailures;

    // ⚠️ Renommé pour éviter le conflit
    public $customErrors = [];

    public function model(array $row)
    {
        // Vérifie si un utilisateur avec ce même identifiant existe déjà
        $existing = User::where('identifiant', $row['identifiant'])->first();

        if ($existing) {
            $this->customErrors[] = "⚠️ L'utilisateur '{$row['nom']}' existe déjà (identifiant : {$row['identifiant']})";
            return null; // On saute cette ligne
        }

        // Trouver la direction si elle existe
        $direction = null;
        if (!empty($row['direction'])) {
            $direction = Direction::firstOrCreate(['nom' => $row['direction']]);
        }

        $defaultDirection = Direction::firstOrCreate(['nom' => 'Direction Générale']);

        return new User([
            'prenom'       => $row['prenom'] ?? 'Inconnu',
            'nom'          => $row['nom'] ?? 'Inconnu',
            'sexe'         => strtoupper($row['sexe'] ?? 'H'),
            'identifiant'  => $row['identifiant'] ?? uniqid('ANPTIC-'),
            'role'         => strtolower($row['role'] ?? 'agent'),
            'direction_id' => $direction->id ?? $defaultDirection->id,
            'password'     => Hash::make('password123'),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.identifiant' => 'required',
            '*.nom'         => 'required',
            '*.prenom'      => 'required',
            '*.role'        => 'required',
        ];
    }

    public function onError(Throwable $error)
    {
        $this->customErrors[] = "Erreur d'import : " . $error->getMessage();
    }
}
