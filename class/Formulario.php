<?php 
if (!isset($_SESSION))
    session_start();

class Formulario{
    public $id;
    public $fechaingreso;
	public $fechasalida;
	public $fechasolicitud;
    public $idsala;
    public $nombresala;
    public $placavehiculo;
    public $detalleequipo;
    public $motivovisita;
    public $visitante;
    public $idtramitante;
    public $idautorizador;
    public $idresponsable;
    public $estado;
    public $nombreresponsable;
    public $nombretramitante;
    public $nombreautorizador;
    public $rfc;
    public $cedulav;
    public $nombrev;
    public $empresav;
    	
	function __construct(){
        require_once("conexion.php");
        //error_reporting(E_ALL);
        // Always in development, disabled in production
        //ini_set('display_errors', 1);
    }
	    
    //Agrega formulario 
    function AgregarFormulario(){
        try {                    
            $sql='INSERT INTO formulario(fechaingreso,idsala,fechasalida,placavehiculo,detalleequipo,motivovisita,idresponsable,idautorizador,idtramitante,estado,rfc)'. 
                'VALUES (:fechaingreso,(SELECT sa.ID FROM SALA sa WHERE NOMBRE= :nombresala),:fechasalida,:placavehiculo,'.
                ':detalleequipo,:motivovisita,(SELECT id FROM responsable WHERE nombre= :nombreresponsable),'.
                '(SELECT id FROM usuario WHERE nombre= :nombreautorizador),(SELECT id FROM usuario WHERE nombre= :nombretramitante),:estado,:rfc)';
            $param= array(':fechaingreso'=>$this->fechaingreso,
                          ':nombresala'=>$this->nombresala,
                          ':fechasalida'=>$this->fechasalida,
                          ':placavehiculo'=>$this->placavehiculo,
                          ':detalleequipo'=>$this->detalleequipo,
                          ':motivovisita'=>$this->motivovisita,
                          ':nombreresponsable'=>$this->nombreresponsable,
                          ':nombreautorizador'=>$this->nombreautorizador,
                          ':nombretramitante'=>$this->nombretramitante,
                          ':estado'=>$this->estado,
                          ':rfc'=>$this->rfc);            
            $result = DATA::Ejecutar($sql,$param);
            
            //Captura el id del formulario
            $idformulario = DATA::$conn->lastInsertId();
            //Convierte el string en un arreglo
            $visitantearray = explode(",",$this->visitante);            
            //Calcula la longitud del arreglo de visistantes
            $longitud = count($visitantearray);
            //Recorre el arreglo e inserta cada item en la tabla intermedia
            for($i=0; $i<$longitud; $i++){
                
                $sql='INSERT INTO visitanteporformulario(idvisitante,idformulario) VALUES (:idvisitante,:idformulario)';
                $param= array(':idvisitante'=>$visitantearray[$i],':idformulario'=>$idformulario); 
                $result = DATA::Ejecutar($sql,$param);
            }
            
            header('Location:../ListaFormulariox.php');
            exit;
        }     
        catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-agregar&id='.$e->getMessage());
            exit;
        }
    }
    
        function Modificar(){
        try {                    
            $sql="UPDATE formulario SET fechaingreso=:fechaingreso,fechasalida=:fechasalida,idtramitante=(SELECT id FROM usuario WHERE nombre= :nombretramitante),
            idautorizador=(SELECT id FROM usuario WHERE nombre= :nombreautorizador),idresponsable=(SELECT id FROM responsable WHERE nombre= :nombreresponsable),placavehiculo=:placavehiculo,
            detalleequipo=:detalleequipo,motivovisita=:motivovisita,estado=:estado,idsala=(SELECT ID FROM SALA WHERE NOMBRE= :nombresala),rfc=:rfc WHERE id=:identificador";
            $param= array(':fechaingreso'=>$this->fechaingreso,
                          ':fechasalida'=>$this->fechasalida,
                          ':nombretramitante'=>$this->nombretramitante,
                          ':nombreautorizador'=>$this->nombreautorizador,
                          ':nombreresponsable'=>$this->nombreresponsable,
                          ':placavehiculo'=>$this->placavehiculo,
                          ':detalleequipo'=>$this->detalleequipo,
                          ':motivovisita'=>$this->motivovisita,
                          ':estado'=>$this->estado,
                          ':nombresala'=>$this->nombresala,
                          ':rfc'=>$this->rfc,
                          ':identificador'=>$this->id);            
            $result = DATA::Ejecutar($sql,$param);

            //Elimina los registros de acuerdo al ID del Formulario
            $sql="DELETE FROM visitanteporformulario WHERE idformulario=:identificador";
            $param= array(':identificador'=>$this->id);            
            $result = DATA::Ejecutar($sql,$param);

            //Convierte el string en un arreglo
            $visitantearray = explode(",",$this->visitante);            
            //Calcula la longitud del arreglo de visistantes
            $longitud = count($visitantearray);
            //Recorre el arreglo e inserta cada item en la tabla intermedia
            for($i=0; $i<$longitud; $i++){         
                $sql='INSERT INTO visitanteporformulario(idvisitante,idformulario) VALUES (:idvisitante,:idformulario)';
                $param= array(':idvisitante'=>$visitantearray[$i],':idformulario'=>$this->id); 
                $result = DATA::Ejecutar($sql,$param);
            }
                        
            header('Location:../ListaFormulariox.php');
            exit;
        }     
        catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-agregar&id='.$e->getMessage());
            exit;
        }
    }
    
    //Consulta formulario para llenar tabla 
    function ConsultaFormulario(){
        try {
			$sql = "SELECT id,fechasolicitud,estado,motivovisita,fechaingreso,fechasalida,idtramitante,
            idautorizador,idresponsable,(SELECT nombre from sala WHERE id=idsala),placavehiculo,detalleequipo,rfc
            FROM formulario";
			$result = DATA::Ejecutar($sql);
			return $result;			
		}catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }		 	
    } 
    
    function Cargar(){
        try {
			$sql = "SELECT id,fechasolicitud,estado,motivovisita,fechaingreso,fechasalida,idtramitante,
            idautorizador,(SELECT nombre from responsable WHERE id=idresponsable),(SELECT nombre from sala WHERE id=idsala),placavehiculo,detalleequipo,rfc
            FROM formulario WHERE id = :identificador";
            
            $param= array(':identificador'=>$this->id);            
            $result = DATA::Ejecutar($sql,$param);
            
			return $result;		
		}catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }	 	
	} 

    function CargaVisitanteporFormulario(){
        try {
			$sql="SELECT v.cedula,v.nombre,v.empresa from visitante v inner join visitanteporformulario vpf 
            on v.cedula=vpf.idvisitante and vpf.idformulario=:identificador";
            $param= array(':identificador'=>$this->id);            
            $result = DATA::Ejecutar($sql,$param);
            
			return $result;		
		}catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }	 	
	} 
    
    function EnviareMail($idvisitante){
        // smtpapl.correo.ice
        // puerto 25
        // ip 10.149.20.26
        // ICETEL\OperTI
        // Clave: Icetel2017
        // Buzón: OperacionesTI@ice.go.cr
       try{
            //consulta datos del visitante
            include("Visitante.php");        
            $visitante= new Visitante();
            $data= $visitante->Cargar($idvisitante);     
            //
            $nombre="";
            $empresa="";
            if (count($data)){ 
                $nombre= $data[0]['NOMBRE'];
                $empresa= $data[0]['EMPRESA'];
            }
            //
            ini_set('SMTP','smtpapl.correo.ice');
            //$to = "ZZT OFICINA PROCESAMIENTO <ofproc1@ice.go.cr>";
            $to= "cchaconc@ice.go.cr";   
            $from = "operTI@ice.go.cr";
            $asunto = "Formulario de Ingreso Pendiente";
            $mensaje = "<h2><i>FORMULARIO DE INGRESO<i><h2>";
            $mensaje .= '<html><body>';
            $mensaje .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
            $mensaje .= "<tr style='background: #eee;'><td><strong>ID:</strong> </td><td>". $idvisitante ."</td></tr>";
            $mensaje .= "<tr><td><strong>Nombre:</strong> </td><td>" .  $nombre  . "</td></tr>";
            $mensaje .= "<tr><td><strong>Empresa:</strong> </td><td>" . $empresa . "</td></tr>";
            $mensaje .= "<tr><td><strong>Detalle:</strong> </td><td>" . $this->motivovisita . "</td></tr>";
            $mensaje .= "<tr><td><strong>Link:</strong> </td><td>" . "http://10.149.20.26:8000//san_pedro_dcti_bitacora/formularioingreso.php?ID=" . $this->id . "</td></tr>";
            $mensaje .= "</table>";
            $mensaje .= "</body></html>";
            //
            $headers = "MIME-Version: 1.0\r\n"; 
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: ".$from."\r\n"; 
            //
            //mail($to, $asunto, $mensaje,$headers);      
                       
        }     
        catch(Exception $e) {
            $_SESSION['errmsg']= $e->getMessage() . " Notificar a Operaciones";
            header('Location: ../Error.php');
            exit;
        }
    }
    
    function AgregarTemporal($visitante){
       try {
            //idtramitador hard coded
            $this->idtramitante=0;
            //agrega infomación del formulario temporal
            $sql='insert into FORMULARIO (FECHAINGRESO,FECHASALIDA,FECHASOLICITUD,IDSALA, MOTIVOVISITA, IDTRAMITANTE ) '.
                ' VALUES (NOW(),DATE_ADD(NOW(), INTERVAL 1 DAY), NOW(), (SELECT sa.ID FROM SALA sa WHERE NOMBRE= :nombresala)  '.
                ' , :motivovisita, :idtramitante )';
            $param= array(
                ':nombresala'=>$this->nombresala,
                ':motivovisita'=>$this->motivovisita,
                ':idtramitante'=>$this->idtramitante
            );            
            $result = DATA::Ejecutar($sql,$param);
            //busca id de formulario agregado
            $sql='SELECT LAST_INSERT_ID() as ID';
            $data= DATA::Ejecutar($sql);
            $this->id =$data[0]['ID'];
            //agrega visitantes
            $sql='insert into VISITANTEPORFORMULARIO VALUES(:idvisitante,:idformulario)';
            $param= array(':idvisitante'=>$visitante,':idformulario'=>$this->id);
            $data=  DATA::Ejecutar($sql,$param);       
            $this->EnviareMail($visitante);
            // elimina sesion link para evitar redirect a paginas anteriores.
            unset($_SESSION['link']);  
            session_destroy();
            header('Location: ../index.php?msg=pendiente');
            exit;
        }     
        catch(Exception $e) {
            $_SESSION['errmsg']= $e->getMessage();
            header('Location: ../Error.php');
            exit;
        }
    }

}


?>