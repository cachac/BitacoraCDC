<?php
if (!isset($_SESSION)) {
    session_start();
}

class Formulario
{
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
    public $nombretramitante;
    public $idautorizador;
    public $nombreautorizador;
    public $idresponsable;
    public $nombreresponsable;
    public $nombreestado;
    public $estado;
    public $rfc;
        
    function __construct()
    {
        require_once("conexion.php");
        //error_reporting(E_ALL);
        // Always in development, disabled in production
        //ini_set('display_errors', 1);
    }
        
    //Agrega formulario
    function AgregarFormulario()
    {
        try {
            $sql="INSERT INTO formulario(fechaingreso,idsala,fechasalida,placavehiculo,detalleequipo,motivovisita,idresponsable,idautorizador,idtramitante,idestado,rfc)
                VALUES (:fechaingreso,(SELECT sa.ID FROM SALA sa WHERE NOMBRE= :nombresala),:fechasalida,:placavehiculo,
                :detalleequipo,:motivovisita,(SELECT id FROM responsable WHERE nombre= :nombreresponsable),
                (SELECT id FROM usuario WHERE nombre= :nombreautorizador),(SELECT id FROM usuario WHERE nombre= :nombretramitante),:estado,:rfc)";
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
            $result = DATA::Ejecutar($sql, $param);
            //Captura el id del formulario
            $idformulario = DATA::$conn->lastInsertId();
            //Convierte el string en un arreglo
            $visitantearray = explode(",", $this->visitante);
            //Calcula la longitud del arreglo de visistantes 
            $longitud = count($visitantearray);
            //Recorre el arreglo e inserta cada item en la tabla intermedia
            for ($i=0; $i<$longitud; $i++) {
                $sql='INSERT INTO visitanteporformulario(idvisitante,idformulario) VALUES (:idvisitante,:idformulario)';
                $param= array(':idvisitante'=>$visitantearray[$i],':idformulario'=>$idformulario);
                $result = DATA::Ejecutar($sql, $param);
            }
            
            header('Location:../ListaFormulario.php');
            exit;
        } catch (Exception $e) {
            header('Location: ../Error.php?w=visitante-agregar&id='.$e->getMessage());
            exit;
        }
    }
    
    function Modificar()
    {
        try {
            $sql="UPDATE formulario SET fechaingreso=:fechaingreso,fechasalida=:fechasalida,idtramitante=(SELECT id FROM usuario WHERE nombre= :nombretramitante),
            idautorizador=(SELECT id FROM usuario WHERE nombre= :nombreautorizador),idresponsable=(SELECT id FROM responsable WHERE nombre= :nombreresponsable),placavehiculo=:placavehiculo,
            detalleequipo=:detalleequipo,motivovisita=:motivovisita,idestado=:estado,idsala=(SELECT ID FROM SALA WHERE NOMBRE= :nombresala),rfc=:rfc WHERE id=:identificador";
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
            $result = DATA::Ejecutar($sql, $param);
            // sesion del formulario temporal
            
            //Convierte el string en un arreglo
            $visitantearray = explode(",", $this->visitante);


            //$entradaexiste="SELECT id FROM visitanteporformulario WHERE ";

            //Elimina los registros segun el arreglo de visitantes
            $sql="DELETE FROM visitanteporformulario WHERE NOT FIND_IN_SET(idvisitante,:EXCLUSION) 
            AND idformulario=:idformulario";
            $param= array(':EXCLUSION'=>$this->visitante,
            ':idformulario'=>$this->id);

            $result = DATA::Ejecutar($sql, $param);
            
            $longitud = count($visitantearray);

            //Recorre el arreglo e inserta cada item en la tabla intermedia
            for ($i=0; $i<$longitud; $i++) {
                //Si no existe Inserta
                $existe="SELECT id from visitanteporformulario WHERE idvisitante = :idvisitante AND idformulario = :idformulario";
                $parametro= array(':idvisitante'=>$visitantearray[$i],':idformulario'=>$this->id);
                $resultadoexiste= DATA::Ejecutar($existe, $parametro);

                if(count($resultadoexiste)==0){
                    $sql="INSERT INTO visitanteporformulario(idvisitante,idformulario) VALUES(:idvisitante,:idformulario)";
                    $param= array(':idvisitante'=>$visitantearray[$i],':idformulario'=>$this->id);
                    $result = DATA::Ejecutar($sql, $param);
                }
            }       
            header('Location:../ListaFormulario.php');
            exit;
        } catch (Exception $e) {
            header('Location: ../Error.php?w=visitante-agregar&id='.$e->getMessage());
            exit;
        }
    }
    
    //Consulta formulario para llenar tabla
    function ConsultaFormulario()
    {
        try {
            $sql = "SELECT id,fechasolicitud,motivovisita,(SELECT nombre FROM estado WHERE id=idestado),fechaingreso,fechasalida,(SELECT nombre FROM usuario WHERE id=idtramitante),
            (SELECT nombre FROM usuario WHERE id=idautorizador),idresponsable,(SELECT nombre from sala WHERE id=idsala),placavehiculo,detalleequipo,rfc
            FROM formulario ORDER BY id DESC;";
            $result = DATA::Ejecutar($sql);
            return $result;
        } catch (Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }
    }

    function ConsultaVisitantePorFormulario($idvisitante)
    {
        try{
            $sql="SELECT f.id as ID , f.fechaingreso , f.fechasalida , f.idestado  as estado
                FROM formulario f inner join visitanteporformulario vf on f.id=vf.idformulario 
                where vf.idvisitante= :idvisitante
                order by f.id desc limit 1 ";
            $param= array(':idvisitante'=>$idvisitante);
            $data = DATA::Ejecutar($sql,$param);
            if (count($data)) {  
                $this->id= $data[0]['ID'];
                $this->fechaingreso= $data[0]['fechaingreso'];
                $this->fechasalida= $data[0]['fechasalida'];                
                $this->estado= $data[0]['estado'];
                return true;
            }
            else{
                return false;
            }
        }catch (Exception $e) {
            header('Location: ../Error.php?w=formulario');
            exit;
        }

    }

    function Cargar()
    {
        try {
            $sql = "SELECT id,fechasolicitud,idestado,motivovisita, 
                DATE_FORMAT(fechaingreso, '%Y-%m-%dT%H:%i') as fechaingreso,
                DATE_FORMAT(fechasalida, '%Y-%m-%dT%H:%i') as fechasalida,(
                SELECT nombre from usuario u inner join formulario f on f.idtramitante=u.id
                where f.id=:identificador)as nombretramitante , (
                SELECT nombre from usuario u inner join formulario f on f.idautorizador=u.id
                where f.id=:identificador) as nombreautorizador, (
                SELECT nombre from responsable r inner join formulario f on f.idresponsable=r.id
                where f.id=:identificador) as nombreresponsable,(
                SELECT sa.nombre FROM sala sa inner join formulario fo on sa.id=fo.idsala 
                where fo.id=:identificador) as nombresala ,
                placavehiculo,detalleequipo, rfc
            FROM formulario WHERE id = :identificador";

            $param= array(':identificador'=>$this->id);
            $data = DATA::Ejecutar($sql, $param);
            //
            if (count($data)) {
                $this->fechasolicitud= $data[0]['fechasolicitud'];
                $this->estado= $data[0]['idestado'];
                $this->motivovisita= $data[0]['motivovisita'];
                $this->fechaingreso= $data[0]['fechaingreso'];
                $this->fechasalida= $data[0]['fechasalida'];
                $this->nombretramitante= $data[0]['nombretramitante'];
                $this->nombreautorizador= $data[0]['nombreautorizador'];
                $this->nombreresponsable= $data[0]['nombreresponsable'];
                $this->nombresala= $data[0]['nombresala'];
                $this->placavehiculo= $data[0]['placavehiculo'];
                $this->detalleequipo= $data[0]['detalleequipo'];
                $this->rfc= $data[0]['rfc'];
            }
            //
            return $data;
        } catch (Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }
    }

    function CargaVisitanteporFormulario()
    {
        try {
            $sql="SELECT DISTINCT v.cedula,v.nombre,v.empresa from visitante v inner join visitanteporformulario vpf 
            on v.cedula=vpf.idvisitante and vpf.idformulario=:identificador";
            $param= array(':identificador'=>$this->id);
            $result = DATA::Ejecutar($sql, $param);
            
            return $result;
        } catch (Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }
    }
    
    function AgregarTemporal($idvisitante)
    {
        try {
            //agrega infomación del formulario temporal
            $sql="insert into FORMULARIO (FECHAINGRESO,FECHASALIDA,FECHASOLICITUD,IDSALA, MOTIVOVISITA, IDTRAMITANTE) 
                VALUES (NOW(),DATE_ADD(NOW(), INTERVAL 1 DAY), NOW(), (SELECT sa.ID FROM SALA sa WHERE NOMBRE= :nombresala), :motivovisita, 
                (SELECT u.id FROM usuario u where u.usuario=:usuario)) ";
            $param= array(
                ':nombresala'=>$this->nombresala,
                ':motivovisita'=>$this->motivovisita,
                ':usuario'=>$_SESSION['username']
             );
             $data= DATA::Ejecutar($sql, $param, true);
            if ($data) {
                 //busca id de formulario agregado
                 $sql='SELECT LAST_INSERT_ID() as ID';
                 $data= DATA::Ejecutar($sql);
                 $this->id =$data[0]['ID'];
                 //agrega visitantes
                 $sql='insert into VISITANTEPORFORMULARIO(idvisitante,idformulario) VALUES(:idvisitante,:idformulario)';
                 $param= array(':idvisitante'=>$idvisitante,':idformulario'=>$this->id);
                 $data=  DATA::Ejecutar($sql, $param);
                 include_once("email.php");
                 email::Enviar($idvisitante, $this->id, "Formulario de Ingreso Pendiente", "FORMULARIO DE INGRESO PENDIENTE");
                 // elimina sesion link para evitar redirect a paginas anteriores.
                 unset($_SESSION['link']);
                 $_SESSION['estado']='pendiente';
                 header('Location: ../index.php');
                 exit;
            } else {
                  $_SESSION['errmsg']= 'Formulario no registrado, comunicarse con operaciones TI';
                header('Location: ../Error.php');
            }
        } catch (Exception $e) {
            $_SESSION['errmsg']= $e->getMessage();
            header('Location: ../Error.php');
            exit;
        }
    }
}
