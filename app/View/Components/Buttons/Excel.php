<?php

namespace App\View\Components\Buttons;

use Illuminate\View\Component;

class Excel extends Component
{
    protected $route;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $data = [
            'route'=>$this->route
        ];
        return view('components.buttons.excel', $data);
    }

}
