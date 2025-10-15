<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\ResendEmailService;

class TestResendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:resend-emails {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Resend email functionality with mock data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $testEmail = $this->option('email') ?: 'test@example.com';
        
        $this->info('Testing Resend Email Integration...');
        $this->info('Using test email: ' . $testEmail);
        $this->newLine();

        try {
            $emailService = new ResendEmailService();
            
            // Test 1: Driver Approval Email
            $this->info('1. Testing Driver Approval Email...');
            $result1 = $emailService->sendDriverApprovalEmail($testEmail, 'John Doe');
            $this->displayResult($result1);
            $this->newLine();

            // Test 2: Vehicle Assignment Email
            $this->info('2. Testing Vehicle Assignment Email...');
            $result2 = $emailService->sendVehicleAssignmentEmail($testEmail, 'John Doe', 'AB12 CDE', 'Toyota Prius');
            $this->displayResult($result2);
            $this->newLine();

            // Test 3: Fine Notification Email
            $this->info('3. Testing Fine Notification Email...');
            $result3 = $emailService->sendFineNotificationEmail($testEmail, 'John Doe', 'Speeding Violation', 150.00, '2025-11-15');
            $this->displayResult($result3);
            $this->newLine();

            // Test 4: Generic Email
            $this->info('4. Testing Generic Email...');
            $htmlContent = '<h1>Test Email</h1><p>This is a test email from PCO Flow.</p>';
            $textContent = 'Test Email\n\nThis is a test email from PCO Flow.';
            $result4 = $emailService->sendEmail($testEmail, 'Test Email from PCO Flow', $htmlContent, $textContent);
            $this->displayResult($result4);
            $this->newLine();

            $this->info('All email tests completed!');
            
        } catch (\Exception $e) {
            $this->error('Error testing emails: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Display the result of an email test
     */
    private function displayResult(array $result)
    {
        if ($result['success']) {
            $this->info('✅ Email sent successfully!');
            if (isset($result['resend_id'])) {
                $this->line('   Resend ID: ' . $result['resend_id']);
            }
        } else {
            $this->error('❌ Email failed to send');
            $this->line('   Error: ' . $result['message']);
            if (isset($result['error_code'])) {
                $this->line('   Error Code: ' . $result['error_code']);
            }
        }
    }
}

