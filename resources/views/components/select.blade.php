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
])

<div class="{{ $class }}">
    <x-label required="{{ $required ? true : false }}" value="{{ $label }}" />
    {{ Form::select($name, $items, $selected, ['class' => 'form-control ' . $classControl . ' ' . ($errors->has($name) ? ' is-invalid' : ''), 'id' => $id, 'disabled' => $disabled]) }}
    @error($name)
        <div class="invalid-feedback font-weight-bold">
            {{ $message }}
        </div>
    @enderror
</div>
