$(document).ready(inicio);
var r=false;
function inicio(){	
    $(".detalle").hide();
    $("#enviar").click(onValidaIndex);
    $("#enviarPerfil").click(onValidaPerfil);
}
function onValidaIndex(){
    var cedula= document.getElementById('cedula').value;
    if(cedula=="") {
        $("#cedula").attr({placeholder:"REQUERIDO"});
        $("#cedula").focus();
        return false;
    }    
    else if(cedula.length<9) {
        $("#error").css("visibility", "visible"); 
        //$("#error").fadeIn(5000);
        $("#cedula").focus();
        return false;
    }        
    //almacena ingreso y valida si es salida
    //require('Visitante.php');
    //$visitante= new Visitante();
    //$visitante-> Existe();
    //muestra el campo detalle
    
}

function onValidaPerfil(){
    var formlisto=true;
    var cedula= document.getElementById('cedula').value;
    var empresa= document.getElementById('empresa').value;
    var nombre= document.getElementById('nombre').value;
    if(cedula=="") {
        $("#cedula").attr({placeholder:"REQUERIDO"});
        $("#cedula").focus();
        formlisto=false;
    }    
    else if(cedula.length<9) {
        $("#error").css("visibility", "visible"); 
        //$("#error").fadeIn(5000);
        $("#cedula").focus();
        formlisto=false;
    }        
    if(empresa=="") {
        $("#empresa").attr({placeholder:"REQUERIDO"});
        $("#empresa").focus();
        formlisto=false;
    }    
    if(nombre=="") {
        $("#nombre").attr({placeholder:"REQUERIDO"});
        $("#nombre").focus();
        formlisto=false;
    }
    if(formlisto) return true;
    else return false;
    
}

function MuestraDetalle(){
    if(!r){
        $(".detalle").addClass("Muestradetalle");
        $(".detalle").fadeIn(1000);
        $(".textarea-field").focus();
        r=true;
        return false      
    }
    else {
        //$("#ingreso").css("visibility", "visible");
        //$("#ingreso").slideUp( 300 ).delay( 800 ).fadeIn( 400 );
            
        return true;
    }    
}

/*function onCorre(){
	$("#ingreso").animate({left:'50px', opacity:1},5000, onRegresa);	
}
function onRegresa(){
	$("#ingreso").animate({left:'900px', opacity:1},5000);	
}*/
    
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}