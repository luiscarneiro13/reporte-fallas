<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-user-circle"></i> {{ ucfirst($resource) }}</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route($resource . '.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i>
                    Agregar</a>
            </div>
        </div>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive">
        <div class="form-inline float-right" style="margin-bottom:20px">
            <form>
                <label>
                    Buscar:&nbsp;
                    <input type="search" name="query" class="form-control form-control-sm"
                        value="{{ @request()->get('query') }}">
                    <button class="btn btn-dark btn-xs ml-2"><i class="fa fa-search"></i></button>
                </label>
            </form>
        </div>
        <table class="table table-striped table-bordered w-100 tabla-{{ $resource }}">
            <thead>
                <tr>
                    @foreach ($heads as $key => $value)
                        <th>{{ @$key }}</th>
                    @endforeach
                    <th width="15%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        @foreach ($heads as $key => $value)
                            @if ($key == 'Tienda' && @$item->$value != null)
                                <td>
                                    <a target="_blank"
                                        href="{{ 'https://vemitienda.com/catalogo' . '/' . $item->$value }}">Catálogo</a>
                                    -
                                    <a target="_blank"
                                        href="{{ 'https://vemitienda.com/' . $item->$value }}">Tienda</a>
                                </td>
                            @else
                                @if ($key == 'Imagen' && isset($item->$value->url))
                                    <td>
                                        <img height="30px" src="{{ env('APP_URL') . '/' . $item->$value->url }}" alt="">
                                    </td>
                                @else
                                    <td>{{ @$item->$value }}</td>
                                @endif
                            @endif
                        @endforeach

                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <a class="dropdown-toggle btn btn-sm btn-dark" data-toggle="dropdown"
                                        aria-expanded="false">
                                        {{-- Opciones --}}
                                    </a>
                                    <div class="dropdown-menu" style="">

                                        @if (@$edit)
                                            <a class="dropdown-item"
                                                href="{{ route($resource . '.edit', $item->id) }}"><i
                                                    class="fa fa-edit"></i>
                                                Editar</a>
                                        @endif

                                        @if (@$show)
                                            <a class="dropdown-item"
                                                href="{{ route($resource . '.edit', $item->id) }}"><i
                                                    class="fa fa-eye"></i>
                                                Ver datos</a>
                                        @endif

                                        <div class="dropdown-divider"></div>

                                        <form class="formEliminar" data-nombre="{{ $item->label }}"
                                            action="{{ route($resource . '.destroy', $item->id) }}" method="post">
                                            <button class="dropdown-item" type="submit"><i class="fa fa-trash"></i>
                                                Eliminar</button>
                                            <input type="hidden" name="_method" value="delete" />
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="{{ $heads->count() + 1 }}"">No hay datos
                            disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (!request()->get('query'))
            <div style=" margin-top: 20px">
                <div class="float-left" style="margin-top: 10px">
                    Mostrando {{ $data->perPage() }} registros de un total de
                    {{ $data->total() }}
                </div>
                <div class="float-right table-responsive">
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
