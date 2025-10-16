@extends('driver_dashboard.layouts.app')

@section('title')
    <title>Driver Profile | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item text-sm text-dark active"><a href="{{url('/driver-profile')}}" aria-current="page">Driver Profile</a></li>
@endsection

@section('content')

<!-- Extra padding for driver profile page only -->
<div style="padding-top: 100px;"></div>

<div class="row">
    <!-- Profile Overview Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Profile Overview</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-user-circle text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h4 class="mb-2">{{ $user->name }}</h4>
                        <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>{{ $user->email }}</p>
                        <p class="text-muted mb-1"><i class="fas fa-id-card me-2"></i>Driver ID: {{ $user->getMeta('emp_id') ?? 'N/A' }}</p>
                        <p class="text-muted mb-1"><i class="fas fa-car me-2"></i>License: {{ $user->getMeta('license_number') ?? 'N/A' }}</p>
                        <span class="badge {{ $user->getMeta('is_verified') == '1' ? 'bg-success' : 'bg-warning' }}">
                            <i class="fas {{ $user->getMeta('is_verified') == '1' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                            {{ $user->getMeta('is_verified') == '1' ? 'Verified Driver' : 'Pending Verification' }}
                        </span>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#passwordChangeModal">
                                <i class="fas fa-key me-1"></i>
                                Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Personal Details</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-edit text-primary"></i>
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

                <form id="profileForm" method="POST" action="{{ route('driver.profile.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->getMeta('mobno')) }}">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license_number" value="{{ $user->getMeta('license_number') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emp_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="emp_id" value="{{ $user->getMeta('emp_id') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contract_number" class="form-label">Contract Number</label>
                            <input type="text" class="form-control" id="contract_number" value="{{ $user->getMeta('contract_number') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">License Issue Date</label>
                            <input type="date" class="form-control" id="issue_date" value="{{ $user->getMeta('issue_date') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="exp_date" class="form-label">License Expiry Date</label>
                            <input type="date" class="form-control" id="exp_date" value="{{ $user->getMeta('exp_date') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Contract Start Date</label>
                            <input type="date" class="form-control" id="start_date" value="{{ $user->getMeta('start_date') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Contract End Date</label>
                            <input type="date" class="form-control" id="end_date" value="{{ $user->getMeta('end_date') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $user->getMeta('address')) }}</textarea>
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="verification_status" class="form-label">Verification Status</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $user->getMeta('is_verified') == '1' ? 'Verified' : 'Pending Verification' }}" readonly>
                                <span class="input-group-text">
                                    <i class="fas {{ $user->getMeta('is_verified') == '1' ? 'fa-check-circle text-success' : 'fa-clock text-warning' }}"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Contract & License Information -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Contract & License Information</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-file-contract text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">Contract Number</label>
                            <p class="mb-0 fw-bold">{{ $user->getMeta('contract_number') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">License Number</label>
                            <p class="mb-0 fw-bold">{{ $user->getMeta('license_number') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">License Issue Date</label>
                            <p class="mb-0 fw-bold">{{ $user->getMeta('issue_date') ? \Carbon\Carbon::parse($user->getMeta('issue_date'))->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">License Expiry Date</label>
                            <p class="mb-0 fw-bold {{ $user->getMeta('exp_date') && \Carbon\Carbon::parse($user->getMeta('exp_date'))->isPast() ? 'text-danger' : '' }}">
                                {{ $user->getMeta('exp_date') ? \Carbon\Carbon::parse($user->getMeta('exp_date'))->format('M d, Y') : 'N/A' }}
                                @if($user->getMeta('exp_date') && \Carbon\Carbon::parse($user->getMeta('exp_date'))->isPast())
                                    <i class="fas fa-exclamation-triangle text-danger ms-1" title="License Expired"></i>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">Contract Start Date</label>
                            <p class="mb-0 fw-bold">{{ $user->getMeta('start_date') ? \Carbon\Carbon::parse($user->getMeta('start_date'))->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">Contract End Date</label>
                            <p class="mb-0 fw-bold">{{ $user->getMeta('end_date') ? \Carbon\Carbon::parse($user->getMeta('end_date'))->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <label class="form-label text-muted">Verification Status</label>
                            <p class="mb-0">
                                <span class="badge {{ $user->getMeta('is_verified') == '1' ? 'bg-success' : 'bg-warning' }}">
                                    <i class="fas {{ $user->getMeta('is_verified') == '1' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                                    {{ $user->getMeta('is_verified') == '1' ? 'Verified' : 'Pending Verification' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Profile Button -->
<div class="row" style="padding-top: 20px;">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            <button type="submit" form="profileForm" class="btn btn-primary" style="background-color: var(--pco-primary); border-color: var(--pco-primary); color: white; padding: 12px 30px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-save me-2"></i>
                Update Profile
            </button>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-pco-primary text-white">
                <h5 class="modal-title" id="passwordChangeModalLabel">
                    <i class="fas fa-key me-2"></i>
                    Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordChangeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-pco-primary" onclick="changePassword()">
                    <i class="fas fa-save me-1"></i>
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>

@section('css')
<style>
    .btn-primary {
        background-color: var(--pco-primary) !important;
        border-color: var(--pco-primary) !important;
        color: white !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        border-radius: 6px !important;
        transition: all 0.3s ease !important;
    }
    
    .btn-primary:hover {
        background-color: #021a1f !important;
        border-color: #021a1f !important;
        color: white !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(3, 33, 39, 0.3) !important;
    }
    
    .btn-primary i {
        font-size: 14px !important;
    }
    
    /* Remove extra white space beneath Contract & License Information card */
    .main-content .container-fluid {
        padding-bottom: 0 !important;
    }
    
    .footer {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    /* Reduce padding beneath Contract & License Information card by 90% */
    .row:last-child {
        margin-bottom: 0 !important;
    }
    
    .card:last-child {
        margin-bottom: 0 !important;
    }
    
    /* Target the Contract & License Information card specifically */
    .card:last-child .card-body {
        padding-bottom: 0.5rem !important;
    }
    
    /* Remove all bottom spacing from the last card */
    .card:last-child .card-body .row:last-child {
        margin-bottom: 0 !important;
    }
    
    .card:last-child .card-body .row:last-child .col-md-6 {
        margin-bottom: 0 !important;
    }
    
    .card:last-child .card-body .row:last-child .info-item {
        margin-bottom: 0 !important;
    }
</style>
@endsection

@section('script')
<script>
    // Change password
    function changePassword() {
        const form = document.getElementById('passwordChangeForm');
        const formData = new FormData(form);
        
        // Validate passwords match
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('new_password_confirmation');
        
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('Password must be at least 8 characters long!');
            return;
        }

        fetch('{{ route("driver.change.password.profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('passwordChangeModal'));
                modal.hide();
                // Clear form
                form.reset();
            } else {
                alert(data.message || 'An error occurred while changing password.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while changing password.');
        });
    }
</script>
@endsection
