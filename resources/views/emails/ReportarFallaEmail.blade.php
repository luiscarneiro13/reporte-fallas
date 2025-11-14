<!DOCTYPE html>
<html>

<head>
    <title>Correo de Prueba</title>
</head>

<body>
    <h1>¡Se ha reportado una falla!</h1>
    <p><strong>Reportada por:</strong> {{ $reported_by_name }}</p>
    <p><strong>Equipo:</strong> {{ $equipment_name }}</p>
    <p><strong>Falla: </strong>{{ $description }}</p>
</body>

</html>
