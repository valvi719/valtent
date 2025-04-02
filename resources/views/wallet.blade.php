<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<body>
<div class="parent-container">
    <div class="wallet-container">
        <h2>Your Wallet</h2>
        <div class="balance">Balance: ₹<span id="balance"><?php if($wallet){ echo $wallet->balance; }else { echo 0;}?></span></div>
        
        <!-- Deposit Section -->
        <div class="input-box" id="deposit-box" style="display:none;">
        <input type="number" id="deposit-amount" placeholder="Enter amount to deposit" required>
        <button class="button" onclick="depositAmount()">Deposit</button>
        </div>

        <!-- Withdraw Section -->
        <div class="input-box" id="withdraw-box" style="display:none;">
        <input type="number" id="withdraw-amount" placeholder="Enter amount to withdraw" required>
        <button class="button" onclick="withdrawAmount()">Withdraw</button>
        </div>

        <!-- Action Buttons -->
        <button class="button" onclick="showDepositBox()">Deposit</button>
        <button class="button" onclick="showWithdrawBox()">Withdraw</button>
    </div>
  </div>
</div>
<script>
    window.razorpayKey = "{{ env('RAZORPAY_KEY_ID') }}"; // Assign Razorpay Key to JS global object
    window.name = "{{ auth()->user()->name }}";
    window.email = "{{ auth()->user()->email }}";
    window.contact = "{{ auth()->user()->phone }}";
</script>
<script src="{{ asset('js/wallet.js') }}"></script>
</body>
@endsection
</html>
