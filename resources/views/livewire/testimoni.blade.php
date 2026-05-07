<main>
    <section class="section teams">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12">
				<div class="section-title text-center">
					<p class="text-primary text-uppercase fw-bold mb-2">Apa kata mereka?</p>
					<h1 class="mb-3">Experience Travel dengan ADW</h1>
					<p class="mb-3">
						{{-- Lorem ipsum dolor sit amet, consectetur adipiscing elit.
						Egestas cursus pellentesque dignissim dui, congue. Vel etiam ut. --}}
					</p>

					{{-- Rata-rata rating (opsional) --}}
					@if($averageRating)
						<div class="d-flex justify-content-center align-items-center gap-2 mt-2">
							<div>
								@php
									$roundedAverage = round($averageRating, 1);
								@endphp

								@for ($i = 1; $i <= $maxStars; $i++)
									@if ($i <= floor($averageRating))
										<i class="fas fa-star text-warning"></i>
									@elseif ($i - $averageRating > 0 && $i - $averageRating < 1)
										<i class="fas fa-star-half-alt text-warning"></i>
									@else
										<i class="far fa-star text-warning"></i>
									@endif
								@endfor
							</div>
							<small class="text-muted ms-2">
								{{ $roundedAverage }} / {{ $maxStars }} dari {{ $testimonis->count() }} testimoni
							</small>
						</div>
					@endif
				</div>
			</div>
		</div>

		<div class="row position-relative mt-4">
			@forelse($testimonis as $testimoni)
				<div class="col-xl-4 col-lg-4 col-md-6 mt-4">
					<div class="card bg-transparent border-0 h-100 shadow-sm">
						<div class="card-body d-flex flex-column">
							{{-- Rating bintang --}}
							<div class="mb-2">
								@for ($i = 1; $i <= $maxStars; $i++)
									@if ($i <= $testimoni->star_count)
										<i class="fas fa-star text-warning"></i>
									@else
										<i class="far fa-star text-warning"></i>
									@endif
								@endfor
							</div>

							{{-- Isi testimoni --}}
							<p class="mb-3" style="min-height: 72px;">
								“{{ $testimoni->content }}”
							</p>

							{{-- User yang memberi testimoni --}}
							<div class="mt-auto">
								<h6 class="mb-0 fw-bold">
									{{ optional($testimoni->user)->name ?? 'User #' . $testimoni->user_id }}
								</h6>
								<small class="text-muted">
									{{-- Jika ingin ada info tambahan, misal "Pelanggan" --}}
									Pelanggan
								</small>
							</div>
						</div>
					</div>
				</div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted mt-4">Belum ada testimoni.</p>
                        </div>
                    @endforelse
		    </div>
	    </div>
    </section>


</main>
