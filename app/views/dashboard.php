<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="page-shell">
    <section class="panel">
        <h1>Panel de control</h1>
        <p class="subtitle">Resumen de tu cuenta y accesos rápidos.</p>

        <ul class="info-list">
            <li><strong>Nombre:</strong> <?= $user['name'] ?></li>
            <li><strong>Email:</strong> <?= $user['email'] ?></li>
            <li><strong>Fecha de registro:</strong> <?= $user['created_at'] ?></li>
        </ul>

        <div class="actions">
            <a class="btn-secondary" href="index.php?controller=dashboard&action=audit">Ver auditoría</a>
            <a class="btn-primary" href="index.php?controller=dashboard&action=logout">Cerrar sesión</a>
        </div>
    </section>
</div>
</body>
</html>
