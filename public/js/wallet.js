let balance = 0; // Starting balance (could be fetched from the database)
    var razorpayKey = "{{ $razorpayKey }}";
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Function to show deposit input
    function showDepositBox() {
      document.getElementById('deposit-box').style.display = 'block';
      document.getElementById('withdraw-box').style.display = 'none';
    }

    // Function to show withdraw input
    function showWithdrawBox() {
      document.getElementById('withdraw-box').style.display = 'block';
      document.getElementById('deposit-box').style.display = 'none';
    }

    // Call deposit function
    function depositAmount() {
      let amount = document.getElementById('deposit-amount').value;
      if (amount <= 0 || isNaN(amount)) {
        alert("Please enter a valid amount");
        return;
      }

      // Call Razorpay API to process the payment (create order on the backend first)
      createRazorpayOrder(amount, 'deposit');
    }

    // Call withdraw function
    function withdrawAmount() {
      let amount = document.getElementById('withdraw-amount').value;
      if (amount <= 0 || isNaN(amount)) {
        alert("Please enter a valid amount");
        return;
      }
      
        // Send data to backend via AJAX
        fetch('/transfer-funds', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
            } else {
                alert(data.error || "Something went wrong.");
            }
        })
        .catch(error => {
            alert("Error: " + error);
        });
    }
    
    // Function to create Razorpay order
    function createRazorpayOrder(amount, type) {
      fetch('/create-razorpay-order', {  // API endpoint to create Razorpay order on the backend
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken // Add CSRF token here
        },
        body: JSON.stringify({ amount: amount, type: type }),
      })
      .then(response => response.json())
      .then(data => {
        var options = {
          "key": razorpayKey, // Replace with your Razorpay key
          "amount": amount * 100, // Amount in paise (100 paise = 1 INR)
          "currency": "INR",
          "order_id": data.order_id,
          "name": "Your App Name",
          "description": type === 'deposit' ? "Deposit to Wallet" : "Withdraw from Wallet",
          "image": "{{ asset('images/valtent_logo.jpeg') }}", // Your logo URL
          "handler": function(response) {
            // Process the payment ID and send it back to the backend for verification
            fetch('/process-razorpay-payment', {  // API endpoint to verify Razorpay payment on the backend
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Add CSRF token here
              },
              body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_signature: response.razorpay_signature,
                type: type,
                amount: amount,
              }),
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert(type.charAt(0).toUpperCase() + type.slice(1) + " Successful!");
                if(type == 'deposit')
                { 
                    var currentBalance = parseFloat(document.getElementById('balance').innerText);
                    var depositAmount = parseFloat(document.getElementById('deposit-amount').value);
                    if (isNaN(depositAmount) || depositAmount <= 0) {
                           alert('Please enter a valid deposit amount!');
                           return;
                    }
                    var newBalance = currentBalance + depositAmount;
                    document.getElementById('balance').innerText = newBalance.toFixed(2);

                }else{
                    var currentBalance = parseFloat(document.getElementById('balance').innerText);
                    var withdrawAmount = parseFloat(document.getElementById('withdraw-amount').value);
                    if (isNaN(withdrawAmount) || withdrawAmount <= 0) {
                           alert('Please enter a valid withdraw amount!');
                           return;
                    }
                    var newBalance = currentBalance - withdrawAmount;
                    document.getElementById('balance').innerText = newBalance.toFixed(2);
                }
                // balance += type === 'deposit' ? parseFloat(amount) : -parseFloat(amount);
                // document.getElementById('balance').innerText = balance.toFixed(2);
              } else {
                alert(data.message || "Something went wrong.");
              }
            });
          },
          "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}",
            "contact": "{{ auth()->user()->phone }}",
          },
          "theme": {
            "color": "#28a745",
          },
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
      })
      .catch(error => {
        console.error("Error creating Razorpay order:", error);
      });
    }