<?php

if (!isset($_SESSION)) {
    session_start();
}

if(isset($_POST["action"])){
    if($_POST["action"]=="SeleccionarDataCenter"){
            $DataCenter= new DataCenter();
            $DataCenter->SeleccionarDataCenter();
    }
    if($_POST["action"]=="Default"){
            $DataCenter= new DataCenter();
            $DataCenter->DataCenterporDefecto();
    }
}

class DataCenter{
    function __construct(){   
    require_once("Conexion.php");
    
    }

    //CONSULTA TODOS LOS DATA CENTERS
    function SeleccionarDataCenter(){
        try {
            $sql='SELECT id,nombre FROM datacenter order by nombre asc';         
            $data = DATA::Ejecutar($sql);
            if (count($data)) {
                $this->id= $data[0]['id'];
                $this->nombre= $data[0]['nombre'];
            }
            echo json_encode($data);			
        }catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }
    }

    //CONSULTA EL DATACENTER POR DEFECTO SAN PEDRO
    function DataCenterporDefecto(){
        try {
            $sql="SELECT id,nombre FROM datacenter WHERE nombre =:sanpedro";   
            $param= array(':sanpedro'=>"SAN PEDRO");      
            $data = DATA::Ejecutar($sql,$param);
            if (count($data)) {
                $this->id= $data[0]['id'];
                $this->nombre= $data[0]['nombre'];
            }
            echo json_encode($data);			
        }catch(Exception $e) {
            header('Location: ../Error.php?w=visitante-bitacora&id='.$e->getMessage());
            exit;
        }
    }

}
    


?>