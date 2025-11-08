<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Setup Error - PCO Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Payment Setup Error</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $message ?? 'An error occurred while accessing the payment setup page.' }}</p>
                        <p class="text-muted small">Please contact support if you need assistance.</p>
                        <a href="mailto:support@pcoflow.com" class="btn btn-primary">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

