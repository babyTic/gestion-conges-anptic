@extends('layouts.dashboard')

@section('title', 'Paramètre RH')
@section('page-title', ' Décision administrative ')

@section('content')
<div class="container">

    <h3>Décision RH (certificats)</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('decision.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Texte de la décision</label>
            <textarea name="decision" class="form-control" rows="4" required>
{{ old('decision', $parametre->valeur) }}
            </textarea>
        </div>

        <button class="btn btn-primary">
            Enregistrer
        </button>
    </form>

</div>
@endsection
