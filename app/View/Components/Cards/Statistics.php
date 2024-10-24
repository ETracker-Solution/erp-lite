<?php

namespace App\View\Components\Cards;

use Illuminate\View\Component;

class Statistics extends Component
{
    protected $title, $value,$icon,$colorClass;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title='Title', $value='Value', $iconClass='layers', $colorClass='bg-light-primary')
    {
        $this->title = $title;
        $this->icon = $iconClass;
        $this->value = $value;
        $this->colorClass = $colorClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $data = [
            'title'=>$this->title,
            'icon'=>$this->icon,
            'value'=>$this->value,
            'colorClass'=>$this->colorClass,
        ];
        return view('components.cards.statistics',$data);
    }
}
