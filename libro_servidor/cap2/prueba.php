<?php include 'Persona.php';
include 'Cliente.php';
    $persona=new Persona("1111111A", "Ana", "Puertas");
    echo $persona->getNombre()." ".$persona->getApellido();
    $persona->setApellido("Montes");
    echo $persona." ".$persona->getApellido()."<br>";
    $Fran=new Cliente("2222222B", "Luis", "Gomez", 1500);
    echo $Fran."<br>";
?>