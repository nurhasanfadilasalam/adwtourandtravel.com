@php
    $galleries = \App\Models\Gallery::query()
        ->where('user_id', auth()->user()->id)
        ->latest()
        ->get();
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse ($galleries as $gallery)
        <div class="rounded-xl overflow-hidden shadow bg-white">
            <img
                src="{{ asset('storage/' . $gallery->link_gambar) }}"
                class="w-full h-40 object-cover"
                loading="lazy"
            >

            <div class="p-2 text-sm">
                <p class="text-gray-700 line-clamp-2">
                    {{ $gallery->deskripsi }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    {{ $gallery->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    @empty
        <p class="col-span-full text-center text-gray-500">
            Belum ada gallery
        </p>
    @endforelse
</div>
