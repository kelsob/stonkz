<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Portfolio extends Component
{
    public $portfolio;

    public function mount()
    {
        Log::info(Auth::user());
        $this->portfolio = Auth::user()->portfolio->load('portfolioStocks.stock');
    }

    public function render()
    {
        return view('livewire.portfolio', ['portfolio' => $this->portfolio]);
    }
}