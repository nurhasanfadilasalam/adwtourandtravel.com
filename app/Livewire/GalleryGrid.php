<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Gallery as ModelGallery;

class GalleryGrid extends Component
{
    // Tentukan jumlah item yang ditampilkan per halaman (misalnya 9 gambar per halaman untuk 3x3 grid)
    public $perPage = 9; // 9 gambar per halaman

    // Menyimpan status pagination dalam query string
    protected $queryString = ['page'];


    public function render()
    {
        // Ambil data galeri dengan pagination
        $galleries = ModelGallery::latest()->paginate($this->perPage);

        // Menampilkan data ke view Livewire
        return view('livewire.gallery-grid', [
            'galleries' => $galleries
        ]);
    }
}
