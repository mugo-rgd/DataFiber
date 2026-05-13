<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NetworkLocation extends Component
{
    public $record;

    public function __construct($record = null)
    {
        $this->record = $record;
    }

    public function render()
    {
        return view('components.network-location');
    }
}
