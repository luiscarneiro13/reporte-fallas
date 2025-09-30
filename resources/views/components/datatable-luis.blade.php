@php
    $config = [
        'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
        'order' => [],
    ];
@endphp
<x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config" striped hoverable bordered
    compressed>
    @foreach ($data as $item)
        <tr>
            <td>{{ $item->dni }}</td>
            <td>{{ $item->nombre }}</td>
            <td>{{ $item->apellido }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->telefono }}</td>
            <td>{!! $btns !!}</td>
        </tr>
    @endforeach
</x-adminlte-datatable>
