<?php
// formulario_foto.php
// Formulario simple que envía nombre, edad y una foto a mostrar_datos.php
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Subir foto</title>
</head>
<body>
  <h1>Formulario de subida de foto</h1>
  <form action="mostrar_datos.php" method="post" enctype="multipart/form-data">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>
    <br>
    <label for="edad">Edad:</label>
    <input type="number" id="edad" name="edad" required min="0">
    <br>
    <label for="foto">Foto:</label>
    <input type="file" id="foto" name="foto" accept="image/*" required>
    <br>
    <button type="submit">Enviar</button>
  </form>
</body>
</html>
