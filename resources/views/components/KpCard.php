<?php

namespace App\View\Components;

use Illuminate\View\Component;

class KpCard extends Component
{
    public $type;
    public $title;
    public $icon;

    public function __construct($type = 'blue', $title = '', $icon = '')
    {
        $this->type = $type;
        $this->title = $title;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.kp-card');
    }
}
