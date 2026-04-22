@props([
    'id',
    'title',
])

<button
    type="button"
    class="info-button"
    data-bs-toggle="modal"
    data-bs-target="#{{ $id }}"
    aria-label="About {{ $title }}"
>
    i
</button>

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header">
                <h2 class="modal-title h5" id="{{ $id }}Label">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-secondary">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
