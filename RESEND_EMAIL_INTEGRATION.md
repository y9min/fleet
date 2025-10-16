# Resend Email Integration for PCO Flow

This document provides comprehensive information about the Resend email integration implemented in the PCO Flow fleet management system.

## Overview

The integration automatically sends transactional emails to drivers for key events:
- **Driver Approval**: Welcome email when a driver is approved
- **Vehicle Assignment**: Notification when a driver is assigned a vehicle
- **Fine Notification**: Alert when a driver receives a fine

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
RESEND_API_KEY=re_SEu2hB8T_466WwskmsdbjKbf2tETjL8wT
```

### Domain Configuration

The system is configured to send emails from `no-reply@pcoflow.com` (already verified in Resend).

## Implementation Details

### Core Service Class

**File**: `app/Utils/ResendEmailService.php`

The main service class handles all email operations:

```php
use App\Utils\ResendEmailService;

$emailService = new ResendEmailService();

// Send driver approval email
$result = $emailService->sendDriverApprovalEmail($driverEmail, $driverName);

// Send vehicle assignment email
$result = $emailService->sendVehicleAssignmentEmail($driverEmail, $driverName, $vehiclePlate, $vehicleModel);

// Send fine notification email
$result = $emailService->sendFineNotificationEmail($driverEmail, $driverName, $fineType, $amount, $dueDate);

// Send generic email
$result = $emailService->sendEmail($to, $subject, $htmlContent, $textContent);
```

### Email Triggers

#### 1. Driver Approval Email

**Triggered in**: `app/Http/Controllers/Admin/OnboardingController.php` (approve method)

**When**: A driver application is approved and the driver is added to the system

**Email Details**:
- **Subject**: "Welcome to PCO Flow – You're Approved!"
- **Content**: Welcome message with next steps and login instructions

#### 2. Vehicle Assignment Email

**Triggered in**: `app/Http/Controllers/Backend/VehiclesApiController.php` (assign_driver method)

**When**: A driver is assigned to a vehicle

**Email Details**:
- **Subject**: "Vehicle Assigned – Details Inside"
- **Content**: Vehicle details (plate, model) and important instructions

#### 3. Fine Notification Email

**Triggered in**: `app/Http/Controllers/Admin/FinesController.php` (store method)

**When**: A new fine is created and assigned to a driver

**Email Details**:
- **Subject**: "New Fine Issued – Action Required"
- **Content**: Fine details (type, amount, due date) and payment instructions

## Testing

### Console Command

Test all email types with mock data:

```bash
# Test with default email
php artisan test:resend-emails

# Test with specific email
php artisan test:resend-emails --email=your-email@example.com
```

### API Endpoints

Test individual email types via API:

#### 1. Driver Approval Email
```bash
curl -X POST http://your-domain/api/test-email/driver-approval \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "name": "John Doe"
  }'
```

#### 2. Vehicle Assignment Email
```bash
curl -X POST http://your-domain/api/test-email/vehicle-assignment \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "name": "John Doe",
    "vehicle_plate": "AB12 CDE",
    "vehicle_model": "Toyota Prius"
  }'
```

#### 3. Fine Notification Email
```bash
curl -X POST http://your-domain/api/test-email/fine-notification \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "name": "John Doe",
    "fine_type": "Speeding Violation",
    "amount": 150.00,
    "due_date": "2025-11-15"
  }'
```

#### 4. Generic Email
```bash
curl -X POST http://your-domain/api/test-email/generic \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "subject": "Test Email",
    "html_content": "<h1>Test</h1><p>This is a test email.</p>",
    "text_content": "Test\n\nThis is a test email."
  }'
```

## Email Templates

### Driver Approval Email

**HTML Preview**:
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Approval - PCO Flow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">PCO Flow</h1>
        <p style="color: #e0e0e0; margin: 10px 0 0 0; font-size: 16px;">Fleet Management System</p>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #2c3e50; margin-top: 0;">🎉 Congratulations, John Doe!</h2>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            We are excited to inform you that your driver application has been <strong>approved</strong>!
        </p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #27ae60; margin: 20px 0;">
            <h3 style="color: #27ae60; margin-top: 0;">Your account is now active!</h3>
            <p style="margin-bottom: 0;">You can now log in to the PCO Flow driver portal and start receiving bookings.</p>
        </div>
        
        <div style="background: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #2980b9; margin-top: 0;">Next Steps:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Log in to your driver account</li>
                <li>Complete your profile setup</li>
                <li>Upload any required documents</li>
                <li>Set your availability status</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="#" style="background: #27ae60; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Login to Driver Portal</a>
        </div>
    </div>
</body>
</html>
```

### Vehicle Assignment Email

**Key Features**:
- Vehicle details prominently displayed
- Safety instructions
- Professional styling with PCO Flow branding

### Fine Notification Email

**Key Features**:
- Urgent styling with red color scheme
- Clear fine details (type, amount, due date)
- Action required call-to-action
- Payment instructions

## Error Handling

The system includes comprehensive error handling:

1. **Email failures don't break the main process** - If email sending fails, the driver approval, vehicle assignment, or fine creation still succeeds
2. **Detailed logging** - All email attempts are logged with success/failure status
3. **Graceful degradation** - Missing email addresses or API issues are handled gracefully

## Logging

All email operations are logged with the following information:

- **Success logs**: Include Resend ID, recipient, and email type
- **Warning logs**: Include error details for failed sends
- **Error logs**: Include full exception details for debugging

Log entries can be found in the Laravel log files with context about:
- Driver email and name
- Vehicle details (for assignment emails)
- Fine details (for fine notifications)
- Resend API response IDs

## Security Considerations

1. **API Key Protection**: The Resend API key is stored in environment variables
2. **Input Validation**: All email inputs are validated before sending
3. **Rate Limiting**: API routes include throttling to prevent abuse
4. **Error Information**: Error messages don't expose sensitive information

## Monitoring

To monitor email delivery:

1. **Check Laravel logs** for email send attempts
2. **Use Resend dashboard** to view delivery statistics
3. **Monitor API responses** for success/failure status

## Troubleshooting

### Common Issues

1. **"RESEND_API_KEY environment variable is not set"**
   - Ensure the API key is added to your `.env` file
   - Restart your web server after adding the environment variable

2. **Emails not being sent**
   - Check Laravel logs for error details
   - Verify the API key is correct and active
   - Ensure the domain `pcoflow.com` is verified in Resend

3. **Email delivery issues**
   - Check Resend dashboard for delivery statistics
   - Verify recipient email addresses are valid
   - Check spam folders

### Debug Mode

Enable detailed logging by setting `LOG_LEVEL=debug` in your `.env` file to see more detailed email sending information.

## Future Enhancements

Potential improvements for the email system:

1. **Email Templates Management**: Create a UI for managing email templates
2. **Email Preferences**: Allow drivers to set email notification preferences
3. **Email Analytics**: Track open rates and click-through rates
4. **Template Variables**: Support for more dynamic content in templates
5. **Multi-language Support**: Support for multiple languages in email templates

## Support

For issues related to the email integration:

1. Check the Laravel logs first
2. Verify Resend API key and domain configuration
3. Test using the provided console command or API endpoints
4. Contact the development team with specific error messages and log entries

