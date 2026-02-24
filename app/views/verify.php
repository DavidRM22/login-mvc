<!DOCTYPE html>
<html>
<head>
    <title>Verificación OTP</title>
</head>
<body>

<h2>Verificar Código OTP</h2>

<form method="POST" action="index.php?controller=auth&action=verifyOTP">
    <label>Código:</label><br>
    <input type="text" name="code" maxlength="6" required><br><br>

    <button type="submit">Verificar</button>
</form>

</body>
</html>
