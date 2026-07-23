@props(['value', 'btnAddModalTarget' => null, 'btnAddUrl' => null, 'required' => false])

<div>
    <label class="mt-3" {{ $attributes->merge(['class' => '']) }}>
        {{ $value ?? $slot }}
        @if ($btnAddUrl)
            <a href="{{ $btnAddUrl }}" class="small-box-footer">
                <i class="fas fa-plus-circle"></i>
            </a>
        @elseif ($btnAddModalTarget)
            <a href="#" data-toggle="modal" data-target="{{ $btnAddModalTarget }}" class="small-box-footer">
                <i class="fas fa-plus-circle"></i>
            </a>
        @endif
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
</div>
