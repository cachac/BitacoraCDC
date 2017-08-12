<?php 
if (!isset($_SESSION)) 
    session_start();

if (isset($_GET['Message'])) {
    print '<script type="text/javascript">alert("' . $_GET['Message'] . '");</script>';
}
// Sesion de usuario
include("class/sesion.php");
$sesion = new sesion();
if (!$sesion->estado){
    $_SESSION['url']= explode('/',$_SERVER['REQUEST_URI'])[2];
    header('Location: login.php');
    exit;
}
// es un formulario temporal
$formtemp="NULL";
if(isset($_SESSION['TEMP']))
{
    $formtemp=$_SESSION['TEMP']; // ID del formulario temporal.
    unset($_SESSION['TEMP']);
}

include("class/Formulario.php");
$formulario= new Formulario();
$listaformulario= $formulario->ConsultaFormulario();
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Control de Acceso</title>
    <!-- CSS -->
    <link href="css/estilo.css" rel="stylesheet"/>
    <link href="css/formulario.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="css/datatables.css">
    <!-- JS  -->
    <script src="js/jquery.js" type="text/jscript"></script>
    <script type="text/javascript" charset="utf8" src="js/datatables.js"></script>
    <script src="js/validaciones.js" languaje="javascript" type="text/javascript"></script> 
</head>
<body onload="CargarEstiloTablas();"> 
    <header>
	<h1>LISTA FORMULARIOS</h1>        
    <div id="logo"><img src="img/logoice.png" height="75" > </div>
	</header>
    <div id="general">
        <div id="izquierda">
            <input type="text" id="txtbuscavisitante" name="txtbuscavisitante" class="inputformat" value=""/> 
            <input id="btnbuscaxvisiante" type="button" value="Buscar por Visitante"/>
        </div>
        <div id="principal">
            <div id="superiornavegacion">
                <div id="nuevo">
                    <input type="button" id="btnnuevo" class="cbp-mc-submit" value="Nuevo" onclick="location.href='FormularioIngreso.php'";>      
                </div>
                <div id="atraslista">
                    <input type="button" id="btnatras" class="cbp-mc-submit" value="Atrás"onclick="location.href='MenuAdmin.php'";>   
                </div>
            </div>
            <div id="listavisitante">
               </br>
               <?php 
                print "<table id='listaformulario'class='display'>";
                print "<thead>";
                print "<tr>";
                print "<th>ID</th>";
                print "<th>FECHA SOLICITUD</th>";
                print "<th>MOTIVO</th>";
                print "<th>ESTADO</th>";
                print "<th>RFC</th>";
                print "<th>MODIFICAR</th>";    
                print "</tr>";
                print "</thead>";	
                print "<tbody>";
                for($i=0; $i<count($listaformulario); $i++){
                        print "<tr>";
                        print "<td>".$listaformulario[$i][0]."</td>";
                        print "<td>".$listaformulario[$i][1]."</td>";
                        print "<td>".$listaformulario[$i][2]."</td>";
                        print "<td>".$listaformulario[$i][3]."</td>";
                        print "<td>".$listaformulario[$i][12]."</td>";
                        print "<td><img id=imgdelete src=img/file_mod.png class=modificar></td>";
                        print "</tr>";
                }
                print "</tbody>";
                print "</table>";
                ?>
                </div>
                <footer></footer>  
        </div>
        <div id="derecha">
  
        </div>
    </div>    
    <script>
        
        $(document).ready( function () {
            //Da la apariencia del css datatable
            CargarEstiloTablas();
            //envía notificación al servidor
            this.ajaxSent = function() {
                try {
                    xhr = new XMLHttpRequest();
                } catch (err) {
                    alert(err);
                }
                //alert('enviando formulario temporal: ' + formtemp);
                url='notificaciondinamica.php?msg='+formtemp;
                xhr.open('GET', url, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {                    
                        if (xhr.status == 200) {   
                            formtemp.value = "";
                            //alert('finalizando formulario temporal: ' + formtemp);
                        }
                    }
                };
                xhr.send();
            };

            var formtemp= "<?php echo $formtemp; ?>";
            //alert(formtemp);

            if(formtemp!="NULL")
                this.ajaxSent();
        } );  // fin document ready.
        
        function CargarEstiloTablas() {
            //$('#listaformulario').DataTable({"order": [[ 3, "desc" ]]});
            $('#listaformulario').DataTable();    
        }

        //MODIFICA EL REGISTRO SELECIONADO EN EL CAMPO MODIFICAR *********/       
        $(document).on('click', '.modificar', function (event) {    
            var idtd = $(this).parents("tr").find("td").eq(0).text();
            location.href='FormularioIngreso.php?MOD='+idtd;
        }); 


        $(document).on('click', '#btnbuscaxvisiante', function (event) {
        $.ajax({
            type: "POST",
            url: "class/Formulario.php",
            data: {
                    action: "Consultarporvisitante",
                    cedula: document.getElementById('txtbuscavisitante').value,
                  }
        })
        .done(function( e ) {
            var formularioxvisitante = JSON.parse(e);
            
            $('#listaformulario').html("");
            
            
            //alert(formularioxvisitante.length);
            //location.reload();
        })    
        .fail(function(msg){
            alert("Error al Eliminar");
        });
    });  
         

    </script>
    </body>
</html>

