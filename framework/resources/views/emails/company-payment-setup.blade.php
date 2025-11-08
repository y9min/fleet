<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Setup - PCO Flow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <p>Hi {{ $superAdmin->name }},</p>

    <p>Your company {{ $company->name }} has been added to PCO Flow.<br/>
    To start using the platform, please complete your account setup by adding a payment method.</p>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #495057; margin-top: 0;">SUBSCRIPTION DETAILS</h2>
        <p style="margin: 0;">
            Company: {{ $company->name }}<br/>
            Price per vehicle: £7/month
        </p>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('admin.yamz.billing-portal', $company->id) }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">
            Complete Setup
        </a>
    </div>

    <p>This link opens a secure Stripe billing page where you can add your preferred payment method and manage your subscription.</p>

    <p>If you have any questions, contact the PCO Flow team anytime.</p>

    <p>— PCO Flow Team<br/>
    © {{ date('Y') }} PCO Flow. All rights reserved.</p>
</body>
</html>

