@props([
    'label' => '',
    'name' => '',
    'items' => '',
    'selected' => null,
    'class' => 'col-md-12',
    'classControl' => '',
    'id' => '',
])

<div class="{{ $class }}">
    <x-label value="{{ $label }}" />
    {{ Form::select($name, $items, $selected, ['class' => 'form-control ' . $classControl . ' ' . ($errors->has($name) ? ' is-invalid' : ''), 'id' => $id]) }}
    @error($name)
        <div class="invalid-feedback font-weight-bold">
            {{ $message }}
        </div>
    @enderror
</div>
