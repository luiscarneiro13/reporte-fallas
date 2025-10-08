@props(['value', 'btnAddModalTarget' => null])

<label {{ $attributes->merge(['class' => '']) }}>
    {{ $value ?? $slot }}
    @if ($btnAddModalTarget)
        <a href="#" data-toggle="modal" data-target="{{ $btnAddModalTarget }}" class="small-box-footer">
            <i class="fas fa-plus-circle"></i>
        </a>
    @endif
</label>
