<?php

namespace App\Livewire\Frontend;

use App\Actions\Frontend\GetOrderHistory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.frontend')]
class OrderHistory extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function render()
    {
        return view('livewire.frontend.order-history', [
            'orders' => app(GetOrderHistory::class)->handle(
                userId: (int) Auth::id(),
                perPage: $this->perPage,
            ),
        ]);
    }
}
