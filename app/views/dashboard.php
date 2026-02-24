<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Panel de Control</h2>

<p><strong>Nombre:</strong> <?= $user['name'] ?></p>
<p><strong>Email:</strong> <?= $user['email'] ?></p>
<p><strong>Fecha de registro:</strong> <?= $user['created_at'] ?></p>

<br><br>
<a href="index.php?controller=dashboard&action=audit">
    Ver auditoría
</a>


<br>

<a href="index.php?controller=dashboard&action=logout">
    Cerrar sesión
</a>

</body>
</html>
