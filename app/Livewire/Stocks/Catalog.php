<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stock;
use Illuminate\Support\Facades\Log;


class Catalog extends Component
{
    use WithPagination;

    public $timeScale = "1D";
    public $chartType = "line";

    public function timeScaleChanged($scale) {
        Log::info($scale);
        $this->timeScale = $scale;
    }
    public function chartTypeChanged($type) {
        Log::info($type);
        $this->chartType = $type;
    }
    public function render()
    {
        return view('livewire.stocks.catalog',[
            'stocks' => Stock::paginate(9)
            ]
        );
    }
}
