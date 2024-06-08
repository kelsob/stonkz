<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Portfolio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePortfolioForUser
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        // Assuming Portfolio has a 'user_id' field to link to the user
        Log::info('creating portfolio for new user!');

        Portfolio::create([
            'user_id' => $event->user->id,
            // Add any other necessary default fields for the portfolio
        ]);
    }
}
