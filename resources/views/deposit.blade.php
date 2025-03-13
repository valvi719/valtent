<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<body>
    <h1>Deposit Amount: {{ $amount }}</h1>
    <form action="{{ route('wallet.processDeposit') }}" method="POST">
        @csrf
        <input type="hidden" name="razorpay_payment_id" value="pay_Q4GpngewIMZtuV">
        <button type="submit">Confirm Deposit</button>
    </form>
    <script>
            var options = {
                "key": "{{ env('RAZORPAY_KEY_ID') }}",
                "amount": {{ $amount }} * 100, // Convert to paise
                "currency": "INR",
                "order_id": "{{ $order->id }}", // Order ID created on the backend
                "name": "Your Company",
                "description": "Deposit",
                "handler": function (response) {
                    var payment_id = response.razorpay_payment_id;  // Get the payment ID
                    var order_id = response.razorpay_order_id;

                    // Send the payment ID and order ID to the backend for verification
                    $.ajax({
                        url: "{{ route('wallet.processDeposit') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            razorpay_payment_id: payment_id,
                            razorpay_order_id: order_id
                        },
                        success: function(response) {
                            // Handle the response (success or failure)
                        }
                    });
                },
                "prefill": {
                    "name": "Customer Name",
                    "email": "customer@example.com"
                },
                "theme": {
                    "color": "#F37254"
                }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();

    </script>
</body>
@endsection
</html>
