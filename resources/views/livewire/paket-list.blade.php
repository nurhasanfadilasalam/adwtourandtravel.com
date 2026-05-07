<div>
    <div class="row">
        @foreach ($paketlists as $paketlist)
            <div class="col-xl-4 col-lg-4 col-md-6 mt-4">
                <div class="card bg-transparent border-0 text-center">
                    <div class="card-img" style="height: 450px; overflow: hidden; padding-left: 15px; padding-right: 15px;">
                        <img src="{{ asset('storage/' . $paketlist->thumbnail) }}" alt="Paket Image" class="rounded w-100" width="300" height="300">
                    </div>
                    <div class="card-body">
                        <div class="gallery-description">
                            {!! $paketlist->nama_paket !!}
                        </div>
                        <small class="text-primary">{{ $paketlist->harga_paket }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $paketlists->links('pagination::bootstrap-4') }} <!-- Pagination with Bootstrap styling -->
    </div>
</div>
