<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Gallery as ModelGallery;

class Gallery extends Component
{
        public function render()
    {
        // return view('livewire.gallery');
        // Mengambil data galeri dari database
        $galleries = ModelGallery::latest()->get();

        // Menampilkan view dengan data galeri
        return view('livewire.gallery', [
            'galleries' => $galleries
        ]);
    }
}
