<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conbank;
use Razorpay\Api\Api;
use App\Services\RazorpayService;

class RazorpayController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }
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
   
        if (!isset($input['razorpay_payment_id'])) {
            return response()->json(['error' => 'Razorpay Payment is missing'], 400);
        }

        if (!isset($input['razorpay_order_id'])) {
            return response()->json(['error' => 'Razorpay order_id is missing'], 400);
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
    
    public function showwallet()
    {
        $userCreId = auth()->id();
        // Fetch the wallet using the cre_id (user ID)
        $wallet = Conbank::where('cre_id', $userCreId)->first();
        return view('wallet', ['wallet' => $wallet], [
            'razorpayKey' => env('RAZORPAY_KEY_ID'),
        ] ); 
    }
    // Function to create Razorpay order
    public function createRazorpayOrder(Request $request)
    {
        $amount = $request->input('amount');
        $type = $request->input('type');  // 'deposit' or 'withdraw'

        // Here, you can fetch the user's Conbank record
        $conbank = Conbank::where('cre_id', auth()->user()->id)->first();

        if (!$conbank) {
            return response()->json(['error' => 'Wallet not found!'], 404);
        }

        // Razorpay API credentials
        $key = env('RAZORPAY_KEY_ID');
        $secret = env('RAZORPAY_KEY_SECRET');
        $api = new Api($key, $secret);

        // Create Razorpay order
        try {
            $order = $api->order->create([
                'receipt' => uniqid(),
                'amount' => $amount * 100,  // Razorpay accepts paise (100 paise = 1 INR)
                'currency' => 'INR',
                'payment_capture' => 1,
            ]);

            // Store order id in session or database if needed
            return response()->json([
                'order_id' => $order->id,
                'amount' => $amount * 100,
                'currency' => 'INR',     // Currency type
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Function to process Razorpay payment
    public function processRazorpayPayment(Request $request)
    {
        $paymentId = $request->input('razorpay_payment_id');
        $orderId = $request->input('razorpay_order_id');
        $signature = $request->input('razorpay_signature');
        $type = $request->input('type');
        $amount = $request->input('amount');
        

        // Razorpay API credentials
        $key = env('RAZORPAY_KEY_ID');
        $secret = env('RAZORPAY_KEY_SECRET');
        $api = new Api($key, $secret);

        try {
             // Verify the payment signature
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);

            // Update the wallet balance after the payment
            $conbank = Conbank::where('cre_id', auth()->user()->id)->first();

            if (!$conbank) {
                return response()->json(['error' => 'Wallet not found!'], 404);
            }

            if ($type === 'deposit') {
                $conbank->balance +=  $amount;
                $conbank->deposits += $amount;
            } elseif ($type === 'withdraw' && $conbank->balance >= $amount) {
                $conbank->balance -= $amount;
                $conbank->withdrawals += $amount;
            } else {
                return response()->json(['error' => 'Insufficient balance for withdrawal.'], 400);
            }

            $conbank->save();

            return response()->json(['success' => true, 'message' => $type . ' successful!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment verification failed: ' . $e->getMessage()], 500);
        }
    }
}
