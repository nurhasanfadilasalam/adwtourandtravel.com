<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaketUmroh as PaketData;

class PaketList extends Component
{
    // public function render()
    // {
    //     return view('livewire.paket-list');
    // }
        // Tentukan jumlah item yang ditampilkan per halaman (misalnya 9 gambar per halaman untuk 3x3 grid)
    public $perPage = 3; // 9 gambar per halaman

    // Menyimpan status pagination dalam query string
    protected $queryString = ['page'];

    public function render()
    {
        // Ambil data galeri dengan pagination
        $paketlists = PaketData::latest()->paginate($this->perPage);

        // Menampilkan data ke view Livewire
        return view('livewire.paket-list', [
            'paketlists' => $paketlists
        ]);
    }
}
