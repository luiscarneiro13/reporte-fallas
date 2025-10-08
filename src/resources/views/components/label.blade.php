@props(['value', 'btnAddUrl' => null,])

<label {{ $attributes->merge(['class' => '']) }}>
    {{ $value ?? $slot }}
    @if ($btnAddUrl)
        <a href="{{ $btnAddUrl }}" style="margin-top:-6px">
            <i class="fas fa-plus-circle"></i>
        </a>
    @endif
</label>
