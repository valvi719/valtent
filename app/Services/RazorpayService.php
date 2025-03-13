<?php
namespace App\Services;
use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    }

    public function createOrder($amount)
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new \Exception('Invalid amount provided');
        }
        // Convert amount to paise (multiply by 100)
        $amountInPaise = (int)($amount * 100);  // Ensure it's an integer

        // Log the amount to ensure it's correct
        \Log::info('Creating order with amount: ' . $amountInPaise . ' paise');
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        // Create Razorpay order
        $orderData = [
            'receipt'         => (string)rand(1000, 9999),  // Generate random receipt ID
            'amount'          => $amountInPaise,    // Amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1,                 // Capture payment automatically
        ];

        // dd($orderData);
        \Log::info('Order Data:', $orderData);

        try {
            $order = $api->order->create($orderData);
            
            \Log::info('Razorpay Order Response:', (array) $order);
            return $order;
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            \Log::error('Razorpay Order Creation Error: ' . $e->getMessage());  // Log the error
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }

    public function fetchPayment($paymentId)
    {
        return $this->api->payment->fetch($paymentId);
    }
}
