<!DOCTYPE html>
<html>

<head>
    <title>¡Se ha cerrado una falla!</title>
</head>

<body>
    <h1>¡Se ha cerrado una falla!</h1>
    <p><strong>Reportada por:</strong> {{ $reported_by_name }}</p>
    <p><strong>Equipo:</strong> {{ $equipment_name }}</p>
    <p><strong>Falla: </strong>{{ $description }}</p>

    <p><strong>Ejecución completada: </strong>{{ $completed_execution }}</p>
    <p><strong>Actividad realizada por: </strong>{{ $executor_name }}</p>
    <p><strong>Actividades realizadas al equipo: </strong>{{ $equipment_maintenance_log }}</p>
</body>

</html>
