<?php 
//session_start();
class usuario{
	public $usuario;
	public $contrasena;
    public $idrol;
    public $nombre;
	
	function __construct(){
        require_once("conexion.php");
        require_once("log.php");
    }
	
    function Validar(){    
        $sql='SELECT USUARIO, IDROL FROM usuario where CONTRASENA=:contrasena  AND USUARIO=:usuario';
        $param= array(':usuario'=>$this->usuario, ':contrasena'=>$this->contrasena);        
        $data = DATA::Ejecutar($sql,$param);
        if (count($data) ) {
            $this->idrol= $data[0]['IDROL'];
            log::Add('INFO', 'Inicio de sesión: '. $this->usuario);
            return true;
        }else {        
            return false;           
        }        
    }
    function Cargar(){    
        $sql='SELECT NOMBRE FROM usuario WHERE usuario=:usuario';
        $param= array(':usuario'=>$_SESSION['username']);        
        $data = DATA::Ejecutar($sql,$param);
        if (count($data) ) {
            $this->nombre= $data[0]['NOMBRE'];
            return true;
        }else {        
            return false;           
        }        
    }
}
?>