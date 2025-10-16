<?php
class Persona {
    private $DNI;        // Corregido: "private" en lugar de "prívate"
    private $nombre;     // Corregido: "private" en lugar de "prívate"
    private $apellido;   // Corregido: "private" en lugar de "prívate"
    
    function __construct($DNI, $nombre, $apellido) {  // Corregido: "__construct" con doble guión bajo
        $this->DNI = $DNI;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getApellido() {
        return $this->apellido;
    }
    
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }
    
    public function __toString() {  // Corregido: "__toString" con doble guión bajo
        return "Persona: ".$this->nombre." ".$this->apellido;
    }
}

// crear una persona
//$per = new Persona("1111111A", "Ana", "Puertas");
// mostrarla, usa el método toString()
//echo $per. "<br>";
// cambiar el apellido
//$per->setApellido("Montes");
// volver a mostrar
//echo $per. "<br>";
?>