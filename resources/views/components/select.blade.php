@props([
    'label' => '',
    'name' => '',
    'items' => '',
    'selected' => null,
    'class' => 'col-md-12',
    'classControl' => '',
    'id' => '',
    'required' => false,
    'disabled' => false,
    'btnAddModalTarget' => '',
])

<div class="{{ $class }}">
    <x-label required="{{ $required ? true : false }}" value="{{ $label }}"
        btnAddModalTarget="{{ $btnAddModalTarget }}" />
    {{ Form::select($name, $items, $selected, ['class' => 'form-control ' . $classControl . ' ' . ($errors->has($name) ? ' is-invalid' : ''), 'id' => $id, 'disabled' => $disabled]) }}
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
