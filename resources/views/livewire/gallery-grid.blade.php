<div>
    <div class="row">
        @foreach ($galleries as $gallery)
            <div class="col-xl-4 col-lg-4 col-md-6 mt-4">
                <div class="card bg-transparent border-0 text-center">
                    <div class="card-img">
                        <img src="{{ asset('storage/' . $gallery->link_gambar) }}" alt="Gallery Image" class="rounded w-100" width="300" height="300">
                    </div>
                    <div class="card-body">
                        <div class="gallery-description">
                            {!! $gallery->deskripsi !!}
                        </div>
                        <small class="text-primary">{{ $gallery->upload_by }}</small>
                         <br>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $gallery->created_at->diffForHumans() }}
                            </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $galleries->links('pagination::bootstrap-4') }} <!-- Pagination dengan styling Bootstrap -->
    </div>

</div>
