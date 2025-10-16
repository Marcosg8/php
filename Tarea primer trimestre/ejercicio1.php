<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
      
       
        .tabla-imagenes img {
            width: 68px;
            height: 68px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255,215,0,0.12);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .tabla-imagenes td:hover {
            background: #ffd70033;
        }
        .tabla-imagenes img:hover {
            transform: scale(1.12);
            box-shadow: 0 4px 16px rgba(255,215,0,0.25);
        }
    </style>
</head>
<body>
    <?php
    // Imágenes disponibles
    $imagenes = [
        'images/descarga.jpg',
        'images/descarga (1).jpg',
        'images/descarga (2).jpg'
    ];
    // Crear array de 16 posiciones y asignar imágenes aleatoriamente
    $tabla = [];
    for ($i = 0; $i < 16; $i++) {
        $tabla[] = $imagenes[array_rand($imagenes)];
    }
    echo '<table class="tabla-imagenes">';
    for ($row = 0; $row < 4; $row++) {
        echo '<tr>';
        for ($col = 0; $col < 4; $col++) {
            $pos = $row * 4 + $col;
            echo '<td>';
            echo '<img src="' . $tabla[$pos] . '" alt="img">';
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    ?>
</body>
</html>
