<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class ResendEmailService
{
    private $apiKey;
    private $baseUrl = 'https://api.resend.com';
    private $fromEmail = 'notifications@pcoflow.com';

    public function __construct()
    {
        $this->apiKey = env('RESEND_API_KEY');
        if (!$this->apiKey) {
            throw new \Exception('RESEND_API_KEY environment variable is not set');
        }
    }
    
    /**
     * Make a direct API call to Resend using cURL
     */
    private function callResendApi($endpoint, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('cURL error: ' . $error);
        }
        
        return ['response' => $response, 'code' => $httpCode];
    }

    /**
     * Send email using Resend API
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlContent HTML email content
     * @param string|null $textContent Optional plain text content
     * @return array Result array with success status and message
     */
    public function sendEmail(string $to, string $subject, string $htmlContent, ?string $textContent = null): array
    {
        try {
            $companyName = $this->getCompanyName();
            
            Log::info('Attempting to send email via Resend', [
                'to' => $to,
                'subject' => $subject,
                'from' => $this->fromEmail,
                'company_name' => $companyName
            ]);

            $emailData = [
                'from' => $companyName . ' <' . $this->fromEmail . '>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $htmlContent,
                'reply_to' => 'support@pcoflow.com',
                'headers' => [
                    'X-Priority' => '3',
                    'X-MSMail-Priority' => 'Normal',
                    'Importance' => 'Normal'
                ]
            ];

            if ($textContent) {
                $emailData['text'] = $textContent;
            }

            // Use direct cURL API call instead of SDK
            $apiResult = $this->callResendApi('/emails', $emailData);
            
            if ($apiResult['code'] >= 200 && $apiResult['code'] < 300) {
                $result = json_decode($apiResult['response'], true);
                
                Log::info('Email sent successfully via Resend', [
                    'to' => $to,
                    'subject' => $subject,
                    'resend_id' => $result['id'] ?? 'unknown'
                ]);

                return [
                    'success' => true,
                    'message' => 'Email sent successfully',
                    'resend_id' => $result['id'] ?? null
                ];
            } else {
                throw new \Exception('Resend API error: HTTP ' . $apiResult['code'] . ' - ' . $apiResult['response']);
            }

        } catch (\Exception $e) {
            Log::error('Resend API error', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }

    /**
     * Send driver approval email
     */
    public function sendDriverApprovalEmail(string $driverEmail, string $driverName): array
    {
        $companyName = $this->getCompanyName();
        $subject = "Account Approved - Welcome!";
        
        $htmlContent = $this->getDriverApprovalEmailTemplate($driverName, $companyName);
        $textContent = $this->getDriverApprovalEmailTextTemplate($driverName, $companyName);

        return $this->sendEmail($driverEmail, $subject, $htmlContent, $textContent);
    }

    /**
     * Send vehicle assignment email
     */
    public function sendVehicleAssignmentEmail(string $driverEmail, string $driverName, string $vehiclePlate, string $vehicleModel): array
    {
        $companyName = $this->getCompanyName();
        $subject = "Vehicle Assigned - Check Your Account";
        
        $htmlContent = $this->getVehicleAssignmentEmailTemplate($driverName, $vehiclePlate, $vehicleModel, $companyName);
        $textContent = $this->getVehicleAssignmentEmailTextTemplate($driverName, $vehiclePlate, $vehicleModel, $companyName);

        return $this->sendEmail($driverEmail, $subject, $htmlContent, $textContent);
    }

    /**
     * Send fine notification email
     */
    public function sendFineNotificationEmail(string $driverEmail, string $driverName, string $fineType, float $amount, string $dueDate): array
    {
        $companyName = $this->getCompanyName();
        $subject = "Fine Notice - Check Your Account";
        
        $htmlContent = $this->getFineNotificationEmailTemplate($driverName, $fineType, $amount, $dueDate, $companyName);
        $textContent = $this->getFineNotificationEmailTextTemplate($driverName, $fineType, $amount, $dueDate, $companyName);

        return $this->sendEmail($driverEmail, $subject, $htmlContent, $textContent);
    }

    /**
     * Get company name for current user
     */
    private function getCompanyName(): string
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && $user->company_id) {
                $company = \App\Model\Company::find($user->company_id);
                if ($company) {
                    return $company->name;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not get company name for email: ' . $e->getMessage());
        }
        
        // Fallback to a generic name if no company found
        return 'Your Company';
    }

    /**
     * Driver approval email HTML template
     */
    private function getDriverApprovalEmailTemplate(string $driverName, string $companyName): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Account Approved</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #ffffff; padding: 30px; border: 1px solid #e9ecef; border-radius: 10px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #27ae60; margin: 0; font-size: 32px;">✅ You\'re Approved!</h1>
                    <h2 style="color: #2c3e50; margin: 10px 0 0 0; font-size: 24px;">Welcome to ' . htmlspecialchars($companyName) . '</h2>
                </div>
                
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: #2c3e50; margin: 0; font-size: 20px;">🎉 Congratulations, ' . htmlspecialchars($driverName) . '!</h2>
                    <p style="font-size: 16px; margin: 10px 0 0 0;">
                        Your driver application has been approved, and your account is now active.
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">🚗 What Happens Next</h3>
                    <p style="font-size: 16px; margin: 0;">
                        We\'ll be sending you another email shortly with details on when and where to pick up your vehicle.
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">🔑 Access Your Driver Portal</h3>
                    <p style="font-size: 16px; margin: 0 0 10px 0;">
                        You can now log in using:
                    </p>
                    <p style="font-size: 16px; margin: 5px 0;">
                        <strong>Email:</strong> [Your email address]
                    </p>
                    <p style="font-size: 16px; margin: 5px 0;">
                        <strong>Password:</strong> password (please change this after logging in)
                    </p>
                    
                    <p style="font-size: 16px; margin: 15px 0 10px 0;">
                        In your portal, you can view:
                    </p>
                    <ul style="margin: 0; padding-left: 20px; font-size: 16px;">
                        <li>Assigned vehicle details</li>
                        <li>Upcoming payments</li>
                        <li>Any issued fines or penalties</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="#" style="background: #27ae60; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Log In to Driver Portal</a>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">💬 Need Help?</h3>
                    <p style="font-size: 16px; margin: 0;">
                        If you have any questions, our support team is here to assist you — just reply to this email or contact us anytime.
                    </p>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <p style="font-size: 16px; margin: 0; color: #2c3e50;">
                        Welcome aboard,<br>
                        <strong>The ' . htmlspecialchars($companyName) . ' Team</strong>
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Vehicle assignment email HTML template
     */
    private function getVehicleAssignmentEmailTemplate(string $driverName, string $vehiclePlate, string $vehicleModel, string $companyName): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Vehicle Assigned</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #f8f9fa; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: #2c3e50; margin: 0; font-size: 28px;">Vehicle Assigned</h1>
            </div>
            
            <div style="background: #ffffff; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e9ecef;">
                <p style="font-size: 16px; margin-bottom: 20px;">
                    Hello ' . htmlspecialchars($driverName) . ',
                </p>
                
                <p style="font-size: 16px; margin-bottom: 20px;">
                    You have been assigned a new vehicle. Check your account for details.
                </p>
                
                <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #dee2e6; margin: 20px 0;">
                    <h3 style="color: #495057; margin-top: 0; text-align: center;">Vehicle Details</h3>
                    <div style="text-align: center;">
                        <p style="font-size: 20px; font-weight: bold; color: #2c3e50; margin: 10px 0;">
                            ' . htmlspecialchars($vehiclePlate) . '
                        </p>
                        <p style="font-size: 16px; color: #6c757d; margin: 5px 0;">
                            ' . htmlspecialchars($vehicleModel) . '
                        </p>
                    </div>
                </div>
                
                <p style="font-size: 16px; color: #6c757d;">
                    Please log in to your account to view full vehicle details and instructions.
                </p>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="#" style="background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Check Account</a>
                </div>
            </div>
            
            <div style="text-align: center; padding: 20px; color: #6c757d; font-size: 14px;">
                <p>This is an automated notification. For support, contact us at support@pcoflow.com</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Fine notification email HTML template
     */
    private function getFineNotificationEmailTemplate(string $driverName, string $fineType, float $amount, string $dueDate, string $companyName): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Fine Notice</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #f8f9fa; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: #2c3e50; margin: 0; font-size: 28px;">Fine Notice</h1>
            </div>
            
            <div style="background: #ffffff; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e9ecef;">
                <p style="font-size: 16px; margin-bottom: 20px;">
                    Hello ' . htmlspecialchars($driverName) . ',
                </p>
                
                <p style="font-size: 16px; margin-bottom: 20px;">
                    You have received a fine. Please check your account for details.
                </p>
                
                <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #dee2e6; margin: 20px 0;">
                    <div style="text-align: center;">
                        <p style="font-size: 18px; color: #495057; margin: 10px 0;">
                            <strong>Fine:</strong> ' . htmlspecialchars($fineType) . '
                        </p>
                        <p style="font-size: 20px; color: #2c3e50; margin: 10px 0;">
                            <strong>Amount:</strong> £' . number_format($amount, 2) . '
                        </p>
                        <p style="font-size: 16px; color: #6c757d; margin: 10px 0;">
                            <strong>Due Date:</strong> ' . htmlspecialchars($dueDate) . '
                        </p>
                    </div>
                </div>
                
                <p style="font-size: 16px; color: #6c757d;">
                    Please log in to your account to view full details and make payment.
                </p>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="#" style="background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Check Account</a>
                </div>
            </div>
            
            <div style="text-align: center; padding: 20px; color: #6c757d; font-size: 14px;">
                <p>This is an automated notification. For support, contact us at support@pcoflow.com</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Driver approval email text template
     */
    private function getDriverApprovalEmailTextTemplate(string $driverName, string $companyName): string
    {
        return "✅ You're Approved!\n" .
               "Welcome to {$companyName}\n\n" .
               "🎉 Congratulations, {$driverName}!\n" .
               "Your driver application has been approved, and your account is now active.\n\n" .
               "⸻\n\n" .
               "🚗 What Happens Next\n\n" .
               "We'll be sending you another email shortly with details on when and where to pick up your vehicle.\n\n" .
               "⸻\n\n" .
               "🔑 Access Your Driver Portal\n\n" .
               "You can now log in using:\n" .
               "Email: [Your email address]\n" .
               "Password: password (please change this after logging in)\n\n" .
               "In your portal, you can view:\n" .
               "• Assigned vehicle details\n" .
               "• Upcoming payments\n" .
               "• Any issued fines or penalties\n\n" .
               "[Log In to Driver Portal]\n\n" .
               "⸻\n\n" .
               "💬 Need Help?\n\n" .
               "If you have any questions, our support team is here to assist you — just reply to this email or contact us anytime.\n\n" .
               "Welcome aboard,\n" .
               "The {$companyName} Team";
    }

    /**
     * Vehicle assignment email text template
     */
    private function getVehicleAssignmentEmailTextTemplate(string $driverName, string $vehiclePlate, string $vehicleModel, string $companyName): string
    {
        return "Vehicle Assigned - Check Your Account\n\n" .
               "Hello {$driverName},\n\n" .
               "You have been assigned a new vehicle. Check your account for details.\n\n" .
               "Vehicle Details:\n" .
               "License Plate: {$vehiclePlate}\n" .
               "Model: {$vehicleModel}\n\n" .
               "Please log in to your account to view full vehicle details and instructions.\n\n" .
               "This is an automated notification from {$companyName}. For support, contact us at support@pcoflow.com";
    }

    /**
     * Fine notification email text template
     */
    private function getFineNotificationEmailTextTemplate(string $driverName, string $fineType, float $amount, string $dueDate, string $companyName): string
    {
        return "Fine Notice - Check Your Account\n\n" .
               "Hello {$driverName},\n\n" .
               "You have received a fine. Please check your account for details.\n\n" .
               "Fine Details:\n" .
               "Fine: {$fineType}\n" .
               "Amount: £" . number_format($amount, 2) . "\n" .
               "Due Date: {$dueDate}\n\n" .
               "Please log in to your account to view full details and make payment.\n\n" .
               "This is an automated notification from {$companyName}. For support, contact us at support@pcoflow.com";
    }

    /**
     * Send driver booking invitation email
     */
    public function sendDriverBookingInvitationEmail(
        string $driverEmail, 
        string $driverName, 
        string $customerName,
        string $pickupDate,
        string $pickupTime,
        string $pickupAddress,
        string $vehicleInfo,
        string $additionalInfo = ''
    ): array {
        Log::info('ResendEmailService: Starting driver booking invitation email', [
            'driver_email' => $driverEmail,
            'driver_name' => $driverName,
            'customer_name' => $customerName,
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'pickup_address' => $pickupAddress,
            'vehicle_info' => $vehicleInfo,
            'additional_info' => $additionalInfo
        ]);

        $companyName = $this->getCompanyName();
        $subject = "Vehicle Pickup Instructions";
        
        Log::info('ResendEmailService: Email parameters prepared', [
            'company_name' => $companyName,
            'subject' => $subject,
            'driver_email' => $driverEmail
        ]);
        
        $htmlContent = $this->getDriverBookingInvitationEmailTemplate(
            $driverName, 
            $customerName, 
            $pickupDate, 
            $pickupTime, 
            $pickupAddress, 
            $vehicleInfo, 
            $additionalInfo, 
            $companyName
        );
        
        $textContent = $this->getDriverBookingInvitationEmailTextTemplate(
            $driverName, 
            $customerName, 
            $pickupDate, 
            $pickupTime, 
            $pickupAddress, 
            $vehicleInfo, 
            $additionalInfo, 
            $companyName
        );

        Log::info('ResendEmailService: Templates generated, calling sendEmail', [
            'driver_email' => $driverEmail,
            'subject' => $subject,
            'html_length' => strlen($htmlContent),
            'text_length' => strlen($textContent)
        ]);

        $result = $this->sendEmail($driverEmail, $subject, $htmlContent, $textContent);
        
        Log::info('ResendEmailService: sendEmail completed', [
            'driver_email' => $driverEmail,
            'success' => $result['success'],
            'message' => $result['message']
        ]);

        return $result;
    }

    /**
     * Driver booking invitation email HTML template
     */
    private function getDriverBookingInvitationEmailTemplate(
        string $driverName, 
        string $customerName, 
        string $pickupDate, 
        string $pickupTime, 
        string $pickupAddress, 
        string $vehicleInfo, 
        string $additionalInfo, 
        string $companyName
    ): string {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Vehicle Pickup Instructions</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #ffffff; padding: 30px; border: 1px solid #e9ecef; border-radius: 10px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #3498db; margin: 0; font-size: 32px;">🚗 Vehicle Pickup Instructions</h1>
                    <h2 style="color: #2c3e50; margin: 10px 0 0 0; font-size: 24px;">' . htmlspecialchars($companyName) . '</h2>
                </div>
                
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: #2c3e50; margin: 0; font-size: 20px;">Hello ' . htmlspecialchars($driverName) . '!</h2>
                    <p style="font-size: 16px; margin: 10px 0 0 0;">
                        Your vehicle is now ready to be collected. Please read the instructions below and proceed to the pickup location.
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 Pickup Details</h3>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px 0; font-weight: bold; color: #495057;">Pickup Date:</td>
                                <td style="padding: 8px 0; color: #212529;">' . htmlspecialchars($pickupDate) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: bold; color: #495057;">Pickup Time:</td>
                                <td style="padding: 8px 0; color: #212529;">' . htmlspecialchars($pickupTime) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: bold; color: #495057;">Pickup Address:</td>
                                <td style="padding: 8px 0; color: #212529;">' . htmlspecialchars($pickupAddress) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: bold; color: #495057;">Vehicle:</td>
                                <td style="padding: 8px 0; color: #212529;">' . htmlspecialchars($vehicleInfo) . '</td>
                            </tr>' .
                            ($additionalInfo ? '
                            <tr>
                                <td style="padding: 8px 0; font-weight: bold; color: #495057;">Additional Info:</td>
                                <td style="padding: 8px 0; color: #212529;">' . htmlspecialchars($additionalInfo) . '</td>
                            </tr>' : '') . '
                        </table>
                    </div>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">⚠️ Important Instructions</h3>
                    <ul style="margin: 0; padding-left: 20px; font-size: 16px;">
                        <li>Please arrive at the pickup location 5-10 minutes before the scheduled time</li>
                        <li>Follow all safety protocols and company guidelines</li>
                        <li>Follow all safety protocols and company guidelines</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin: 30px 0; color: #6c757d;">
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 0;">
                </div>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <p style="font-size: 16px; margin: 0; color: #6c757d;">
                        If you have any questions or concerns, please contact us immediately.
                    </p>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                    <p style="font-size: 14px; margin: 0; color: #6c757d;">
                        This is an automated message from ' . htmlspecialchars($companyName) . '<br>
                        For support, contact us at <a href="mailto:support@pcoflow.com" style="color: #3498db;">support@pcoflow.com</a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Driver booking invitation email text template
     */
    private function getDriverBookingInvitationEmailTextTemplate(
        string $driverName, 
        string $customerName, 
        string $pickupDate, 
        string $pickupTime, 
        string $pickupAddress, 
        string $vehicleInfo, 
        string $additionalInfo, 
        string $companyName
    ): string {
        return "Vehicle Pickup Instructions\n\n" .
               "Hello {$driverName}!\n\n" .
               "Your vehicle is now ready to be collected. Please read the instructions below and proceed to the pickup location.\n\n" .
               "Pickup Details:\n" .
               "Pickup Date: {$pickupDate}\n" .
               "Pickup Time: {$pickupTime}\n" .
               "Pickup Address: {$pickupAddress}\n" .
               "Vehicle: {$vehicleInfo}\n" .
               ($additionalInfo ? "Additional Info: {$additionalInfo}\n" : "") . "\n" .
               "Important Instructions:\n" .
               "- Please arrive at the pickup location 5-10 minutes before the scheduled time\n" .
               "- Follow all safety protocols and company guidelines\n\n" .
               "If you have any questions or concerns, please contact us immediately.\n\n" .
               "This is an automated message from {$companyName}. For support, contact us at support@pcoflow.com";
    }
}
