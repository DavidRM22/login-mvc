<!DOCTYPE html>
<html>
<head>
    <title>Auditoría del Sistema</title>
</head>
<body>

<h2>Auditoría del Sistema</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Evento</th>
        <th>Email</th>
        <th>IP</th>
        <th>Fecha</th>
        <th>Detalles</th>
    </tr>

    <?php foreach ($logs as $log): ?>
    <tr>
        <td><?= $log['event'] ?></td>
        <td><?= $log['email'] ?></td>
        <td><?= $log['ip'] ?></td>
        <td><?= $log['created_at'] ?></td>
        <td><?= $log['details'] ?></td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>
