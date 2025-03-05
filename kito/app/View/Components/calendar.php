<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use SebastianBergmann\Type\VoidType;
use Illuminate\Support\Facades\Blade;

class calendar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.calendar');
    }

    // public function boot(): void{
    //     // Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
    // }
}
