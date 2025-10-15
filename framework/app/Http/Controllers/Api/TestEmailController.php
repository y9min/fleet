<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Utils\ResendEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    /**
     * Test driver approval email
     */
    public function testDriverApprovalEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255'
        ]);

        try {
            $emailService = new ResendEmailService();
            $result = $emailService->sendDriverApprovalEmail($request->email, $request->name);
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'resend_id' => $result['resend_id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test driver approval email error', [
                'email' => $request->email,
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test vehicle assignment email
     */
    public function testVehicleAssignmentEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'vehicle_plate' => 'required|string|max:20',
            'vehicle_model' => 'required|string|max:255'
        ]);

        try {
            $emailService = new ResendEmailService();
            $result = $emailService->sendVehicleAssignmentEmail(
                $request->email, 
                $request->name, 
                $request->vehicle_plate, 
                $request->vehicle_model
            );
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'resend_id' => $result['resend_id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test vehicle assignment email error', [
                'email' => $request->email,
                'name' => $request->name,
                'vehicle_plate' => $request->vehicle_plate,
                'vehicle_model' => $request->vehicle_model,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test fine notification email
     */
    public function testFineNotificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date'
        ]);

        try {
            $emailService = new ResendEmailService();
            $result = $emailService->sendFineNotificationEmail(
                $request->email,
                $request->name,
                $request->fine_type,
                $request->amount,
                $request->due_date
            );
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'resend_id' => $result['resend_id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test fine notification email error', [
                'email' => $request->email,
                'name' => $request->name,
                'fine_type' => $request->fine_type,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test generic email
     */
    public function testGenericEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string'
        ]);

        try {
            $emailService = new ResendEmailService();
            $result = $emailService->sendEmail(
                $request->email,
                $request->subject,
                $request->html_content,
                $request->text_content
            );
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'resend_id' => $result['resend_id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test generic email error', [
                'email' => $request->email,
                'subject' => $request->subject,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }
}

