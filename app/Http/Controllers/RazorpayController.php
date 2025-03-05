<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment-form'); // Create a Blade view called 'payment-form.blade.php'
    }
    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        $orderData = [
            'receipt'         => 'rcptid_11',
            'amount'          => 100 * 100,  // Amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1, // Capture payment automatically after payment is done
        ];

        try {
            $order = $api->order->create($orderData);
            return response()->json(['orderId' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('Razorpay Order Creation Error: ' . $e->getMessage());  // Log the error
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $input = $request->all();

        // Check if razorpay_signature exists
        if (!isset($input['razorpay_signature'])) {
            return response()->json(['error' => 'Razorpay signature is missing'], 400);
        }

        // Initialize Razorpay API with the keys
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        try {
            // Verify the payment signature
            $api->utility->verifyPaymentSignature($input);

            // If the signature is valid, proceed with your order processing (e.g., mark the order as paid)
            // You can use $input['razorpay_order_id'] and $input['razorpay_payment_id'] here
            return response()->json(['status' => 'Payment successful']);
        } catch (\Exception $e) {
            // If signature verification fails
            return response()->json(['error' => 'Payment verification failed: ' . $e->getMessage()], 400);
        }
    }
}
