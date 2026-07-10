@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = [
            ['label' => 'Cédula', 'field' => 'identification_number'],
            ['label' => 'Nombre', 'field' => null],
            ['label' => 'Teléfono', 'field' => 'phone_number'],
            ['label' => 'Cargo', 'field' => 'position'],
            ['label' => 'Dirección', 'field' => 'address'],
            ['label' => 'Usuario de sistema', 'field' => null],
            ['label' => 'Rol de sistema', 'field' => null],
            ['label' => '', 'field' => null],
        ];
    @endphp

    <x-base-data-table-search title="Empleados" :items="$employees" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.employees.create') }}" titlePrint="Listado de empleados"
        :sortBy="$sortBy ?? null" :sortDir="$sortDir ?? 'asc'">
        <x-slot name="body">
            @forelse ($employees as $item)
                <tr>
                    <td>{{ $item->identification_number }}</td>
                    <td>{{ $item->last_name . ' ' . $item->first_name }}</td>
                    <td>{{ $item->phone_number }}</td>
                    <td>{{ $item->position }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->users->first()?->email ?? '' }}</td>
                    <td>{{ $item->users->first()?->roles->first()?->name ?? '' }}</td>

                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.employees.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.employees.incidents', $item) }}">
                                        <i class="fa fa-exclamation-triangle">&nbsp;</i>
                                        Incidencias
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.employees.destroy', $item) }}" method="post">
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
        const BASE_PRINT_URL = "{{ route('employees.impAll') }}";

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
                console.error('La ventana emergente fue bloqueada. Por favor, permítala para imprimir.');
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
