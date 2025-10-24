<?php
$cadena_conexion = 'mysql:dbname=empresa;host=127.0.0.1';
$usuario = 'root';
$clave = '';
try {
    $bd = new PDO($cadena_conexion, $usuario, $clave);
    // configurar PDO para lanzar excepciones en errores
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // conexión establecida (no imprimir para evitar salidas en includes)
    
} catch (PDOException $e) {
    echo 'Error con la base de datos: ' . $e->getMessage();
} 