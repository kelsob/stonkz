<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 

class Balance extends Component
{
    public $balance;

    #[On('user-balance-changed')]
    public function userBalanceChanged(){
        $this->balance = number_format(auth()->user()->balance, 2);
    }

    public function mount(){
        $this->balance = number_format(auth()->user()->balance, 2);
    }
    public function render()
    {
        return view('livewire.balance', [
            'balance' => $this->balance,
        ]);
    }
}
