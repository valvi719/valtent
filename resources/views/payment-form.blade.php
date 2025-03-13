<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Razor Payment')
 
@section('content')
<body>
    <h1 class="center-heading">Razorpay</h1>

    <form action="{{ url('payment-success') }}" method="POST" id="payment-form">
        @csrf
        <input type="hidden" name="razorpay_order_id " id="razorpay_order_id ">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        <div id="payment-button" class="mb-4">
            <button type="button" id="pay-button" class="payment-btn">Pay with Razorpay</button>
        </div>
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
            fetch("{{ url('create-order') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({ amount: 100 }) // Amount in INR (100 INR)
                })
                .then(response => response.json())
                .then(data => {
                if (data.orderId) {
                    var options = {
                        "key": "{{ env('RAZORPAY_KEY_ID') }}",  // Razorpay Key
                        "amount": 100 * 100, // Amount in paise
                        "currency": "INR",
                        "order_id": data.orderId, // Order ID returned from backend
                        "handler": function (response) {
                            console.log(response);
                            var form = document.getElementById('payment-form');
                            form.razorpay_order_id .value = data.orderId; // Set the order_id
                            form.razorpay_payment_id.value = response.razorpay_payment_id; // Razorpay payment ID
                            form.razorpay_signature.value = response.razorpay_signature; // Razorpay signature
                            form.submit();  // Submit the form to backend
                        },
                    };

                    var rzp1 = new Razorpay(options);

                    document.getElementById('pay-button').onclick = function (e) {
                        rzp1.open();
                        e.preventDefault();
                    };
                } else {
                    console.log("Error: Order creation failed.");
                    console.log(data); // Log the response from the backend to see the error message
                }
                })
                .catch(error => {
                console.error('Error:', error);
                });
    </script>
    <!-- CSS for the Razorpay Button -->
    <style>
        /* Centering the H1 */
        .center-heading {
            text-align: center; /* Centers the text */
            margin-bottom: 20px; /* Optional: Adds space below the heading */
        }
        .payment-btn {
            width: 100%; /* Ensures full width */
            max-width: 400px; /* Max width limit */
            background-color: #28a745; /* Green background */
            color: #fff; /* White text */
            padding: 15px 20px; /* Add padding for better appearance */
            border: none; /* Remove border */
            border-radius: 8px; /* Rounded corners */
            font-size: 16px; /* Adjust text size */
            cursor: pointer; /* Pointer cursor on hover */
            text-align: center; /* Center text */
            transition: background-color 0.3s ease; /* Smooth hover effect */
            margin: 0 auto; /* Center button horizontally */
            display: block; /* Ensure the button is a block element */
        }

        /* Hover effect */
        .payment-btn:hover {
            background-color: #218838; /* Darker green when hovering */
        }

        /* Focus effect */
        .payment-btn:focus {
            outline: none; /* Remove the focus outline */
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); /* Add a shadow on focus */
        }

        /* For smaller screens, reduce padding */
        @media screen and (max-width: 600px) {
            .payment-btn {
                padding: 12px 15px; /* Smaller padding on smaller screens */
                font-size: 14px; /* Adjust text size */
            }
        }
    </style>
</body>
@endsection
</html>
