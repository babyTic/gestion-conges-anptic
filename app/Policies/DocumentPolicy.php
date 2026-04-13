<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use App\Models\DemandeConge;

class DocumentPolicy
{
    public function view(User $user, Document $document)
    {
        return $user->id === $document->demande->user_id ||
            $user->role === 'admin' ||
            $user->role === 'rh';
    }

    public function upload(User $user, DemandeConge $demande)
    {
        return $user->id === $demande->user_id;
    }

    public function delete(User $user, Document $document)
    {
        return $this->view($user, $document);
    }

    public function viewAny(User $user, DemandeConge $demande)
    {
        return $user->id === $demande->user_id ||
            $user->role === 'admin' ||
            $user->role === 'rh';
    }
}
