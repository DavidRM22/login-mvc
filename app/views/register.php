<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
</head>
<body>

<h2>Registro de Usuario</h2>

<form method="POST" action="index.php?controller=auth&action=doRegister">
    <label>Nombre:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Registrarse</button>
</form>

<br>
<a href="index.php?controller=auth&action=login">Volver al login</a>

</body>
</html>
