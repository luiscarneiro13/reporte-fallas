<table id="table2" style="width: 100%;" class="table table-bordered table-hover table-striped dataTable no-footer"
    role="grid" aria-describedby="table2_info">
    <thead class="thead-dark">
        <tr role="row">
            <th>#</th>
            <th>Producto / Servicio</th>
            <th>Cantidad</th>
            <th>Precio unidad</th>
            <th>Sub Total $</th>
            <th>Sub Total Bs</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $item)
            <tr role="row" class="odd">
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $item->product }}</td>
                <td>{{ $item->qty }}</td>
                <td>Bs. {{ $item->price_bs }}</td>
                <td>$ {{ round($item->sub_total_bs / $item->rate, 2) }}</td>
                <td>Bs. {{ $item->sub_total_bs }}</td>
                {{-- <td>{{ ceil($item->sub_total * $item->rate * 100) / 100 }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
