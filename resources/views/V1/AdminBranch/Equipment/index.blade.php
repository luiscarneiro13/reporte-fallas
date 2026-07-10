@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = [
            ['label' => 'ID', 'field' => 'id'],
            ['label' => 'Código interno', 'field' => 'internal_code'],
            ['label' => 'Tipo', 'field' => 'type'],
            ['label' => 'Placa', 'field' => 'placa'],
            ['label' => 'Marca', 'field' => 'brand_name'],
            ['label' => 'Modelo', 'field' => 'vehicle_model'],
            ['label' => 'Año', 'field' => 'model_year'],
            ['label' => 'Color', 'field' => 'color'],
            ['label' => 'Proyecto', 'field' => null],
            ['label' => '', 'field' => null],
        ];
    @endphp

    <x-base-data-table-search title="Equipos" :items="$equipment" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.equipment.create') }}" titlePrint="Listado de equipos"
        :sortBy="$sortBy ?? null" :sortDir="$sortDir ?? 'asc'">
        <x-slot name="body">
            @forelse ($equipment as $item)
                <tr>
                    <td>{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->internal_code }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->placa }}</td>
                    <td>{{ $item->brand_name }}</td>
                    <td>{{ $item->vehicle_model }}</td>
                    <td>{{ $item->model_year }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->lastProject?->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.equipment.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    @if (isset($item->history) && count($item->history) > 0)
                                        <a class="dropdown-item"
                                            href="{{ route('admin.sucursal.equipment.show', [
                                                'equipo' => $item,
                                                'back_url' => request()->url(),
                                            ]) }}">
                                            <i class="fas fa-history">&nbsp;</i>
                                            Histtórico de fallas
                                        </a>
                                    @endif

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.equipment.destroy', $item) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="dropdown-item" type="submit">
                                            <i class="fa fa-trash">&nbsp;</i>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </x-slot>
    </x-base-data-table-search>



@stop

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
        const BASE_PRINT_URL = "{{ route('equipment.impAll') }}";

        document.getElementById('printButton').addEventListener('click', function() {
            // 1. Obtiene la query string actual con los filtros (sin validación estricta, la validación se hace internamente)
            const queryString = getFilterQueryString(true);

            if (queryString === null) {
                // Si getFilterQueryString devuelve null, significa que falló una validación
                $('#searchInput').focus(); // Vuelve a enfocar para indicar un error
                return;
            }

            // 2. Construye la URL de impresión completa
            let printUrl = BASE_PRINT_URL;
            if (queryString) {
                printUrl += '?' + queryString;
            }

            // 3. Llama a la función de impresión
            abrirEImprimir(printUrl);
        });

        // ------------------------------------------
        // --- LÓGICA DE IMPRESIÓN (abrirEImprimir) ---
        // ------------------------------------------

        /**
         * Abre la URL de impresión en una nueva ventana, llama a la función print()
         * y la cierra automáticamente.
         * @param {string} printUrl - La URL completa (con parámetros de filtro) a imprimir.
         * @returns {void}
         */
        function abrirEImprimir(printUrl) {
            // Abre la URL en una nueva ventana/pestaña
            let printWindow = window.open(printUrl, '_blank');

            // Asegura que la ventana se haya abierto correctamente
            if (printWindow) {
                // Espera a que el contenido de la nueva ventana termine de cargar
                printWindow.onload = function() {
                    try {
                        // 1. Llama a la función de impresión en la nueva ventana
                        printWindow.print();

                        // 2. Cierra la ventana después de un breve retraso
                        // (Necesario porque print() es asíncrono y no siempre se resuelve bien)
                        setTimeout(function() {
                            // Antes de cerrar, verificamos si la ventana sigue abierta
                            if (printWindow && !printWindow.closed) {
                                printWindow.close();
                            }
                        }, 500); // 500ms de espera

                    } catch (e) {
                        console.error('Error al intentar imprimir en la nueva ventana:', e);
                        // Si falla, se deja la ventana abierta para que el usuario pueda interactuar.
                    }
                };
            } else {
                // Usamos console.error en lugar de alert() como indica la restricción.
                console.error('La ventana emergente fue bloqueada. Por favor, permítala para imprimir.');
                // NOTA: En producción, usar un toast o modal para informar al usuario sin usar alert().
            }
        }

        function getFilterQueryString() {
            // 1. Captura del valor de búsqueda
            const searchQuery = document.getElementById('searchInput').value.trim();

            // 2. Construcción de la URLSearchParams
            const newParams = new URLSearchParams();

            // B. Añadir parámetro de BÚSQUEDA
            if (searchQuery !== '') {
                newParams.append('query', searchQuery);
            }

            // C. Añadir parámetros de ORDENAMIENTO (mismos que usa la tabla) para que la
            // impresión respete el orden de columna aplicado, no solo la búsqueda.
            const urlParams = new URLSearchParams(window.location.search);
            const sortBy = urlParams.get('sort_by');
            const sortDir = urlParams.get('sort_dir');
            if (sortBy) {
                newParams.append('sort_by', sortBy);
            }
            if (sortDir) {
                newParams.append('sort_dir', sortDir);
            }

            return newParams.toString();
        }
    </script>
@stop
