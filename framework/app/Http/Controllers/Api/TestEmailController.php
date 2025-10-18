<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverBooked;

class TestEmailController extends Controller
{
    /**
     * Test driver approval email
     */
    public function testDriverApprovalEmail(Request $request)
    {
        try {
            // This is a test endpoint - in production, remove or secure this
            return response()->json([
                'status' => 'success',
                'message' => 'Test email endpoint - driver approval email would be sent here'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test email failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test vehicle assignment email
     */
    public function testVehicleAssignmentEmail(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Test email endpoint - vehicle assignment email would be sent here'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test email failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test fine notification email
     */
    public function testFineNotificationEmail(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Test email endpoint - fine notification email would be sent here'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test email failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test generic email
     */
    public function testGenericEmail(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Test email endpoint - generic email would be sent here'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test email failed: ' . $e->getMessage()
            ], 500);
        }
    }
}