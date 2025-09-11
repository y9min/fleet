<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - {{ config('app.name', 'Fleet Manager') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            text-align: center;
        }
        
        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .success-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .success-body {
            padding: 40px 30px;
        }
        
        .success-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .application-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .next-steps {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin: 30px 0;
            text-align: left;
        }
        
        .next-steps h5 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .next-steps ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .next-steps li {
            margin-bottom: 8px;
            color: #555;
        }
        
        .contact-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .contact-info h6 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .contact-info p {
            margin: 0;
            color: #6c5700;
        }
        
        .home-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 16px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
            margin-top: 20px;
        }
        
        .home-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Application Submitted!</h1>
        </div>
        
        <div class="success-body">
            <div class="success-message">
                {{ $message }}
            </div>
            
            <div class="application-details">
                <h5 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Application Details</h5>
                
                <div class="detail-row">
                    <span class="detail-label">Application ID:</span>
                    <span class="detail-value">#{{ str_pad($driver->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $driver->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $driver->email }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $driver->phone }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">License Number:</span>
                    <span class="detail-value">{{ $driver->license_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status-badge">{{ ucfirst($driver->status) }}</span></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Submitted:</span>
                    <span class="detail-value">{{ $driver->created_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
            </div>
            
            <div class="next-steps">
                <h5><i class="fas fa-route me-2"></i>What happens next?</h5>
                <ul>
                    <li><strong>Review Process:</strong> Our team will review your application and documents within 2-3 business days.</li>
                    <li><strong>Verification:</strong> We may contact you to verify information or request additional documents.</li>
                    <li><strong>Background Check:</strong> We'll conduct a background check and driving record review.</li>
                    <li><strong>Final Decision:</strong> You'll receive an email with our decision and next steps.</li>
                    <li><strong>Onboarding:</strong> If approved, we'll schedule your orientation and training.</li>
                </ul>
            </div>
            
            <div class="contact-info">
                <h6><i class="fas fa-phone me-2"></i>Questions?</h6>
                <p>Contact our recruitment team at <strong>recruiting@company.com</strong> or call <strong>(555) 123-4567</strong></p>
                <p>Reference your Application ID: <strong>#{{ str_pad($driver->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
            </div>
            
            <div class="text-center">
                <a href="{{ url('/') }}" class="home-button">
                    <i class="fas fa-home me-2"></i>Return to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>