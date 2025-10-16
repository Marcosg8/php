<?php
// login.php - formulario de usuario y contraseña
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login</title>
</head>
<body>
  <h1>Acceder</h1>
  <form method="post" action="comprobar.php">
    <label>Usuario: <input type="text" name="user" required></label><br>
    <label>Contraseña: <input type="password" name="pass" required></label><br>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>
