@extends('driver_dashboard.layouts.app')

@section('title')
    <title>Change Password | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item text-sm text-dark active"><a href="{{url('/driver-change-password')}}" aria-current="page">Change Password</a></li>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Change Password</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-key text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('driver.update.password') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password" required minlength="8">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Password must be at least 8 characters long.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                            <a href="{{ url('/driver-dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Password Requirements -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Password Requirements</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        At least 8 characters long
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Contains at least one uppercase letter
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Contains at least one lowercase letter
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Contains at least one number
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Contains at least one special character
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    
    function validatePassword() {
        const password = newPassword.value;
        const confirm = confirmPassword.value;
        
        // Check if passwords match
        if (password !== confirm && confirm !== '') {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
        
        // Check password strength
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Update requirement indicators
        const indicators = document.querySelectorAll('.password-requirement');
        indicators.forEach((indicator, index) => {
            const requirement = Object.values(requirements)[index];
            if (requirement) {
                indicator.classList.remove('text-danger');
                indicator.classList.add('text-success');
            } else {
                indicator.classList.remove('text-success');
                indicator.classList.add('text-danger');
            }
        });
    }
    
    newPassword.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
});
</script>
@endsection
