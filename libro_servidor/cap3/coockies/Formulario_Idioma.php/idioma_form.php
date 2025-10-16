<?php
// idioma_form.php
// Formulario para elegir el idioma y guardar preferencia en cookie 'lang'.

$cookieTTL = time() + 30 * 24 * 60 * 60; // 30 días

// Si se envía el formulario, guardar cookie y redirigir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
    $lang = ($_POST['lang'] === 'en') ? 'en' : 'es';
    setcookie('lang', $lang, $cookieTTL, '/');
    if ($lang === 'en') {
        header('Location: hi.php');
        exit;
    }
    header('Location: hola.php');
    exit;
}

// Si ya tenemos cookie, redirigir automáticamente
if (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'] === 'en' ? 'en' : 'es';
    if ($lang === 'en') {
        header('Location: hi.php');
        exit;
    }
    header('Location: hola.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Elegir idioma</title>
</head>
<body>
  <h1>Elige tu idioma</h1>
  <form method="post" action="idioma_form.php">
    <label><input type="radio" name="lang" value="es" checked> Español</label><br>
    <label><input type="radio" name="lang" value="en"> English</label><br><br>
    <button type="submit">Guardar y continuar</button>
  </form>
</body>
</html>
