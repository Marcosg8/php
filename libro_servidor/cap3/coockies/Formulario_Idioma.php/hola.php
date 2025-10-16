<?php
// hola.php - página en español
// Si no existe cookie 'lang', establecerla a 'es' por defecto
if (!isset($_COOKIE['lang'])) {
    setcookie('lang', 'es', time() + 30 * 24 * 60 * 60, '/');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Hola</title>
</head>
<body>
  <p>hola</p>
  <p><a href="idioma_form.php">Cambiar idioma</a></p>
</body>
</html>
