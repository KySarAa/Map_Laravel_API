@extends('layouts.app')

@section('title', 'Créer une mission')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Nouvelle Mission</h2>

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0">
                    <strong class="d-block mb-1">Erreurs :</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('missions.store') }}" method="POST">
                @csrf

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-bold">Nom de la mission *</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="{{ old('nom') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea id="description" name="description" class="form-control"
                                rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="date_mission" class="form-label fw-bold">Date de la mission *</label>
                            <input type="date" id="date_mission" name="date_mission" class="form-control"
                                value="{{ old('date_mission', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-0">
                            <label for="culture" class="form-label fw-bold">Culture</label>
                            <input type="text" id="culture" name="culture" class="form-control" value="{{ old('culture') }}"
                                placeholder="Ex: Blé, Maïs...">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-grow-1 py-2 fw-bold">Créer la mission</button>
                    <a href="{{ route('missions') }}" class="btn btn-outline-secondary flex-grow-1 py-2 fw-bold">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection