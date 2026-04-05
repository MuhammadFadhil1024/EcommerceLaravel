<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
   public string $typeClasses;

    public function __construct(public string $type = 'success')
    {
        $this->typeClasses = match ($type) {
            'success' => 'text-green-700 bg-green-100',
            'error'   => 'text-red-700 bg-red-100',
            'warning' => 'text-yellow-700 bg-yellow-100',
            'info'    => 'text-blue-700 bg-blue-100',
            default   => 'text-gray-700 bg-gray-100',
        };
    }

    public function render()
    {
        return view('components.alert');
    }
}
