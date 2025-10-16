<?php
require_once 'Persona.php';

class Cliente extends Persona {
    private $sueldo;
    
    public function __construct($DNI, $nombre, $apellido, $sueldo) {
        parent::__construct($DNI, $nombre, $apellido);
        $this->sueldo = $sueldo;
    }
    
    public function getSueldo() {
        return $this->sueldo;
    }
    
    public function setSueldo($sueldo) {
        $this->sueldo = $sueldo;
    }
    
    public function __toString() {
        return parent::__toString() . " - Sueldo: " . $this->sueldo;
    }
}
?>