@extends('driver_dashboard.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-pco-primary text-white text-center py-4">
                    <h4 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        Set New Password
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Please enter your new password below. Make sure it's at least 8 characters long.
                    </p>

                    <form method="POST" action="{{ route('driver.reset.password.submit') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-key me-2"></i>
                                New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your new password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-check-circle me-2"></i>
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Confirm your new password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-pco-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-pco-primary {
    background-color: var(--pco-primary) !important;
}

.btn-pco-primary {
    background-color: var(--pco-primary);
    border-color: var(--pco-primary);
    color: white;
}

.btn-pco-primary:hover {
    background-color: #021a1f;
    border-color: #021a1f;
    color: white;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-control:focus {
    border-color: var(--pco-primary);
    box-shadow: 0 0 0 0.2rem rgba(3, 33, 39, 0.25);
}

.alert {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 20px;
    }
    
    .card {
        margin-top: 50px;
    }
}
</style>
@endsection
