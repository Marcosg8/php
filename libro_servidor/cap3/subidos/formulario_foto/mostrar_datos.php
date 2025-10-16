<?php
// mostrar_datos.php
// Procesa el formulario: recibe nombre, edad y una foto. Guarda la foto en uploads/

// Configuración
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';

// Asegurar que el directorio uploads existe
if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0755, true);
}

// Helper: sanitizar nombre para usar en fichero (quitar espacios y caracteres peligrosos)
function safe_filename($name) {
		// Convertir a ASCII básico, eliminar acentos si hay (requiere intl ext para transliterator, fallback simple)
		$name = preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $name);
		$name = trim($name, '_-');
		if ($name === '') {
				$name = 'usuario';
		}
		return $name;
}

$errors = [];
$savedPath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
		$edad = isset($_POST['edad']) ? trim($_POST['edad']) : '';

		if ($nombre === '') {
				$errors[] = 'El campo nombre es obligatorio.';
		}
		if ($edad === '' || !is_numeric($edad) || intval($edad) < 0) {
				$errors[] = 'Introduce una edad válida.';
		}

		if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
				$errors[] = 'Debes seleccionar una foto.';
		} else {
				$file = $_FILES['foto'];

				if ($file['error'] !== UPLOAD_ERR_OK) {
						$errors[] = 'Error al subir el archivo (code: ' . $file['error'] . ').';
				} else {
						// Validar tipo mime básico
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$mime = finfo_file($finfo, $file['tmp_name']);
						finfo_close($finfo);

						$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
						if (!array_key_exists($mime, $allowed)) {
								$errors[] = 'Tipo de archivo no permitido. Usa JPG, PNG, GIF o WEBP.';
						} else {
								$ext = $allowed[$mime];
								$base = safe_filename($nombre);

								// Evitar sobrescribir: si ya existe, añadir sufijo numérico
								$targetName = $base . '.' . $ext;
								$i = 1;
								while (file_exists($uploadDir . DIRECTORY_SEPARATOR . $targetName)) {
										$targetName = $base . '_' . $i . '.' . $ext;
										$i++;
								}

								$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $targetName;
								if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
										$errors[] = 'No se pudo mover el archivo subido.';
								} else {
										$savedPath = 'uploads/' . $targetName; // ruta relativa para mostrar en HTML
								}
						}
				}
		}
}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Datos enviados</title>
</head>
<body>
	<h1>Resultado del formulario</h1>

	<?php if (!empty($errors)): ?>
		<div style="color: red;">
			<ul>
				<?php foreach ($errors as $e): ?>
					<li><?php echo htmlspecialchars($e); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<p><a href="formulario_foto.php">Volver al formulario</a></p>
	<?php else: ?>
		<?php if ($savedPath): ?>
			<p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
			<p><strong>Edad:</strong> <?php echo intval($edad); ?></p>
			<p><strong>Foto subida:</strong></p>
			<img src="<?php echo htmlspecialchars($savedPath); ?>" alt="Foto de <?php echo htmlspecialchars($nombre); ?>" style="max-width:400px;height:auto;border:1px solid #ccc;">
			<p><a href="formulario_foto.php">Subir otra foto</a></p>
		<?php else: ?>
			<p>No se procesaron datos.</p>
			<p><a href="formulario_foto.php">Volver al formulario</a></p>
		<?php endif; ?>
	<?php endif; ?>

</body>
</html>
