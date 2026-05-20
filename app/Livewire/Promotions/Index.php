<?php

namespace App\Livewire\Promotions;

use App\Models\Promotion;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Computed]
    public function promotions()
    {
        return Promotion::latest()->paginate(10);
    }

    public function render()
    {
        return view('livewire.promotions.index');
    }
}
