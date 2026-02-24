<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

</head>
<body>

<h2>Iniciar Sesión</h2>

<form method="POST" action="index.php?controller=auth&action=doLogin">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Ingresar</button>
</form>

<br>
<a href="index.php?controller=auth&action=register">
    ¿No tienes cuenta? Regístrate
</a>

</body>
</html>
