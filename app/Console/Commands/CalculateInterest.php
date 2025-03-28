<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conbank;
use Carbon\Carbon;

class CalculateInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate 4% yearly interest using last_interest_applied field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Calculating interest...');

        $accounts = Conbank::all();
        $currentDate = Carbon::now();

        foreach ($accounts as $account) {
            $lastInterestApplied = $account->last_interest_applied ? Carbon::parse($account->last_interest_applied) : Carbon::parse($account->created_at);

            $currentDate = Carbon::now();
            // Calculate the number of years since the last interest application
            $years = $lastInterestApplied->diffInYears($currentDate);

            if ($years > 0) {
                // Calculate 4% interest for each year
                $interest = ($account->balance * 4 / 100) * $years;
                
                // Update the balance, interests, and last_interest_applied
                $account->interests += $interest;
                $account->balance += $interest;
                $account->last_interest_applied = $currentDate;
                $account->save();

                $this->info("Interest applied for Account ID {$account->id} for {$years} year(s).");
            } else {
                $this->info("No interest applied for Account ID {$account->id} (less than a year since last application).");
            }
        }

        $this->info('Interest calculation completed.');
    }
}
