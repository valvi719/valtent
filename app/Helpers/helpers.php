<?php

use App\Models\Donator;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getBadgeAttribute')) {
    
    function getBadgeAttribute()
    {
        $total = Donator::where('donator_id', $this->id)->sum('amount');

        if ($total >= 100000) return 'green';
        if ($total >= 50000) return 'blue';
        if ($total >= 25000) return 'orange';
        return 'none';
    }
}

if (!function_exists('getWithdrawalPercentageAttribute')) {
    
    function getWithdrawalPercentageAttribute()
    {
        return match($this->badge) {
            'green' => 100,
            'blue' => 50,
            'orange' => 25,
            default => 10,
        };
    }
}

if (!function_exists('getDonationBadgeStyle')) {
    function getDonationBadgeStyle($userId = null)
    {
        $userId = $userId ?? Auth::id();

        $total = \App\Models\Donator::where('donator_id', $userId)->sum('amount');

        if ($total >= 100000) {
            return [
                'color' => '#046A38',       // Green
                'label' => '₹100K+ Donor',
                'amount' => $total,
            ];
        } elseif ($total >= 50000) {
            return [
                'color' => '#06038D',       // Blue
                'label' => '₹50K+ Donor',
                'amount' => $total,
            ];
        } elseif ($total >= 25000) {
            return [
                'color' => '#FF671F',       // Orange
                'label' => '₹25K+ Donor',
                'amount' => $total,
            ];
        }

        return null;
    }
}