
<!DOCTYPE html>
<html>
<head>
    <title>Driver Onboarding - Fleet Manager</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style>
        body { background-color: #f8f9fa; }
        .onboarding-container { max-width: 600px; margin: 50px auto; padding: 20px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group label { font-weight: 600; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .required { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="onboarding-container">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Driver Onboarding Application</h3>
                    <p class="mb-0">Please complete all required fields</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('onboarding.submit', $onboarding->unique_link) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="license_number">Drivers License Number <span class="required">*</span></label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="drivers_license">Drivers License Upload <span class="required">*</span></label>
                            <input type="file" class="form-control-file" id="drivers_license" name="drivers_license" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>

                        <div class="form-group">
                            <label for="pco_license">PCO License Upload <span class="required">*</span></label>
                            <input type="file" class="form-control-file" id="pco_license" name="pco_license" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>

                        <div class="form-group">
                            <label for="insurance">Insurance Document Upload <span class="required">*</span></label>
                            <input type="file" class="form-control-file" id="insurance" name="insurance" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>
