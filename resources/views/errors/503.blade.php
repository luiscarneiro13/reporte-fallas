<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Servicio No Disponible (503)</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Estilos personalizados para el fondo y la animación */
        .maintenance-bg {
            background-color: #f7fafc; /* Gris claro */
            background-image: radial-gradient(#cbd5e0 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="antialiased maintenance-bg h-screen flex items-center justify-center">

    <div class="max-w-xl mx-auto p-8 bg-white shadow-2xl rounded-xl border-t-4 border-indigo-600 text-center">

        {{-- 💡 Puedes usar aquí el logo de tu aplicación si lo tienes accesible --}}
        {{-- <img src="/ruta/a/tu/logo.svg" alt="Logo" class="h-16 mx-auto mb-6"> --}}

        <div class="flex flex-col items-center justify-center">

            <div class="mb-4 p-4 rounded-full bg-indigo-100">
                {{-- Icono de herramienta (Puedes reemplazarlo con un SVG si quieres) --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>

            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">
                ¡Mantenimiento en Curso!
            </h1>

            <p class="text-xl text-gray-600 mb-6">
                Código de error: <span class="font-mono bg-gray-100 px-2 py-1 rounded">503</span>
            </p>
        </div>

        <p class="text-gray-700 leading-relaxed mb-6">
            Lamentamos las molestias. Estamos realizando **mejoras importantes** y tareas de mantenimiento en nuestro sistema.
        </p>

        <p class="text-lg font-semibold text-indigo-700">
            Estaremos de vuelta en línea lo antes posible.
        </p>

        @if (isset($exception) && App::environment('local'))
            {{-- Muestra información útil si estás en desarrollo --}}
            <div class="mt-8 pt-4 border-t border-gray-200 text-sm text-gray-500">
                <p>Tiempo de inactividad programado:</p>
                <p class="font-mono bg-gray-100 inline-block p-1 rounded mt-1">{{ $exception->getMessage() }}</p>
            </div>
        @endif

        <div class="mt-8 text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Tu Aplicación') }}.
        </div>
    </div>

</body>
</html>
