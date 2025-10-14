@props(['value', 'btnAddModalTarget' => null, 'required' => false])

<div>
    <label class="mt-3" {{ $attributes->merge(['class' => '']) }}>
        {{ $value ?? $slot }}
        @if ($btnAddModalTarget)
            <a href="#" data-toggle="modal" data-target="{{ $btnAddModalTarget }}" class="small-box-footer">
                <i class="fas fa-plus-circle"></i>
            </a>
        @endif
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
</div>
