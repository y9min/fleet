<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Setup - PCO Flow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #007bff; margin-top: 0;">Welcome to PCO Flow</h1>
        <p>Hi {{ $superAdmin->name }},</p>
        <p>Your company <strong>{{ $company->name }}</strong> has been set up on PCO Flow. To activate your subscription and start using the platform, please complete your payment setup.</p>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #495057; margin-top: 0;">Subscription Details</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;"><strong>Company:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{{ $company->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;"><strong>Vehicles:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{{ $vehicleCount }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;"><strong>Price per vehicle:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">£7/month</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;"><strong>Monthly total:</strong></td>
                <td style="padding: 8px 0;"><strong>£{{ number_format($monthlyAmount, 2) }}/month</strong></td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $portalUrl }}" style="background-color: #007bff; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Complete Payment Setup
        </a>
    </div>

    <div style="background-color: #e9ecef; padding: 15px; border-radius: 8px; font-size: 14px; color: #6c757d;">
        <p style="margin: 0 0 10px 0;"><strong>What happens next?</strong></p>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Click the button above to open the secure Stripe Billing Portal</li>
            <li>Add your payment method (credit/debit card)</li>
            <li>View and manage your subscription details</li>
            <li>Your subscription will activate automatically once payment method is added</li>
            <li>You'll be billed at the end of each month based on the number of vehicles</li>
        </ul>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d; text-align: center;">
        <p>If you have any questions, please contact our support team.</p>
        <p style="margin: 5px 0;">© {{ date('Y') }} PCO Flow. All rights reserved.</p>
    </div>
</body>
</html>

