<?php
if (!isset($_SESSION)) {
    session_start();
}
// Sesion de usuario
require_once("class/sesion.php");
$sesion = new sesion();
if (!$sesion->estado) {
    $_SESSION['url']= explode('/', $_SERVER['REQUEST_URI'])[2];
    header('Location: login.php');
    exit;
}
// visitante
require_once("class/Visitante.php");
$visitante= new Visitante();
$data= $visitante->CargarTodos();
/*$id="NULL";
if (isset($_GET['MOD'])) {
    $id=$_GET['MOD'];
    $visitante->Cargar($id);
} */
?>

<html>
<head>
    <meta charset="UTF-8">
    <title>Control de Acceso</title>
   <!-- CSS -->
    <link href="css/estilo.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="css/datatables.css">
    <link href="css/formulario.css" rel="stylesheet"/>
    <!-- JS  -->
    <script src="js/jquery.js" type="text/jscript"></script>
    <script type="text/javascript" charset="utf8" src="js/datatables.js"></script>
    <script src="js/funciones-Visitante.js" languaje="javascript" type="text/javascript"></script> 
</head>

<body> 
    <header>
        <h1>LISTA DE VISITANTES</h1>        
        <div id="logo"><img src="img/logoice.png" height="75" > </div>
    </header>
     <div id="mensajetop">
        <span id="textomensaje"></span>
    </div>

    <div id="general">
        <aside> 
        </aside>

        <section>
           <div id="superiornavegacion">
                <div id="nuevo">
                    <input type="button" id="btnnuevo" class="cbp-mc-submit" value="Nuevo" onclick="AbreModalInsertar()";>      
                </div>
                <div id="atraslista">
                    <input type="button" id="btnatras" class="cbp-mc-submit" value="Atrás" onclick="location.href='MenuAdmin.php'";>   
                </div>
            </div>

            <div id="lista">
               <br><br><br>
                <?php
                    print "<table id='tblLista'>";
                    print "<thead>";
                    print "<tr>";
                    print "<th>CEDULA</th>";
                    print "<th>NOMBRE</th>";
                    print "<th>EMPRESA</th>";
                    print "<th>PERMISO ANUAL</th>";
                    print "<th>MODIFICAR</th>";
                    print "<th>ELIMINAR</th>";
                    print "</tr>";
                    print "</thead>";
                    print "<tbody>";
                    for ($i=0; $i<count($data); $i++) {
                        print "<tr>";
                        print "<td>".$data[$i][0]."</td>";
                        print "<td>".$data[$i][1]."</td>";
                        print "<td>".$data[$i][2]."</td>";
                        print "<td>".$data[$i][3]."</td>";
                        print "<td><img id=imgdelete src=img/file_mod.png class=modificar></td>";
                        print "<td><img id=imgdelete src=img/file_delete.png class=eliminar></td>";
                        print "</tr>";
                    }
                    print "</tbody>";
                    print "</table>";
                ?>
            </div>
        </section>

        <aside> 
        </aside>

    <!-- MODAL FORMULARIO -->
    <div class="modal" id="modal-index">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Información del Visitante</h2>                
            </div>
        
            <!-- Modal body -->
            <div class="modal-body">
                <div id="form">
                    <h1>Nuevo Visitante</h1>

                    <form name="perfil" method="POST" >
                        <label for="cedula"><span class="campoperfil">Cédula / Identificación <span class="required">*</span></span>
                            <input autofocus type="text" maxlength="9" id="cedula" 
                                value= "<?php if ($visitante->cedula!=null) print $visitante->cedula;  ?>" 
                                class="input-field" name="cedula" placeholder="0 0000 0000" title="Número de cédula separado con CEROS"  onkeypress="return isNumber(event)"/>
                        </label>
                        <label for="empresa"><span class="campoperfil">Empresa / Dependencia <span class="required">*</span></span>
                            <input type="text"   style="text-transform:uppercase" 
                                value= "<?php if ($visitante->empresa!=null) print $visitante->empresa; ?>" 
                                class="input-field" name="empresa" value="" id="empresa"/>
                        </label>
                        <label for="nombre"><span class="campoperfil">Nombre Completo <span class="required">*</span></span>
                            <input  type="text" class="input-field" name="nombre" 
                                value= "<?php if ($visitante->nombre!=null) print $visitante->nombre; ?>" id="nombre"/>
                        </label>
                        <label for="permiso"><span class="campoperfil">Tiene permiso de Ingreso Anual? <span class="required">*</span></span>
                            <input type="checkbox" name="permiso" >
                        </label>

                        <nav class="btnfrm">
                            <ul>
                                <li><button type="button" class="btn" onclick="Guardar()" >Guardar</button></li>
                                <li><button type="button" class="btn" onclick="Cerrar()" >Cerrar</button></li>
                            </ul>
                        </nav>

                    </form>
                    
                </div>
            </div>    
            
            <div class="modal-footer">
            </div>

        </div>
    </div>      
    <!-- FIN MODAL -->

    </div>    
    
    </body>
</html>


