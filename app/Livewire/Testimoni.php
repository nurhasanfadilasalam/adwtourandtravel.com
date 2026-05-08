<?php

namespace App\Livewire;

use App\Models\Testimoni as ModelsTestimoni;
use Livewire\Component;

class Testimoni extends Component
{
    // public function render()
    // {
    //     return view('livewire.testimoni');
    // }
    public int $limit = 9;      // maksimal 9 testimoni
    public int $maxStars = 5;   // maksimal 5 bintang

    public function render()
    {
        // $testimonis = Testimoni::with('user')
        //     ->latest()
        //     ->take($this->limit)
        //     ->get();

        $averageRating = ModelsTestimoni::avg('star_count');

        // return view('livewire.testimoni', [
        //     'testimonis' => $testimonis,
        //     'averageRating' => $averageRating,
        // ]);

        $testimoniList = ModelsTestimoni::orderBy('user_id', 'ASC')->get();
        return view('livewire.testimoni', [
            'testimonis' => $testimoniList,
            'averageRating' => $averageRating,
        ]);
    }
}
