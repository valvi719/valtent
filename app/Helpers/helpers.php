<?php

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