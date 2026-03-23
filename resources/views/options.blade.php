@extends('layouts.app')

@section('title', 'Options')

@section('content')
    <div class="row justify-content-center pt-2">
        <div class="col-md-8 col-lg-6">
            <h2 class="mb-4 fw-bold">Options d’affichage</h2>

            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-success">Mode d’interface</h5>
                    <p class="text-muted small mb-3">
                        Choisissez un affichage adapté soit à un poste fixe (PC), soit à un appareil mobile
                        (boutons plus grands, textes plus lisibles).
                    </p>

                    <form action="{{ url('/options/ui-mode') }}" method="POST" class="mb-0">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ui_mode" id="mode_desktop"
                                       value="desktop" {{ $currentMode === 'desktop' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mode_desktop">
                                    Mode ordinateur de bureau
                                    <span class="d-block text-muted small">
                                        Interface plus compacte, idéale sur grand écran.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ui_mode" id="mode_mobile"
                                       value="mobile" {{ $currentMode === 'mobile' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mode_mobile">
                                    Mode appareil mobile
                                    <span class="d-block text-muted small">
                                        Boutons et textes agrandis pour tablette / smartphone.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success px-4 fw-bold">
                                Enregistrer
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                Retour au tableau de bord
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection