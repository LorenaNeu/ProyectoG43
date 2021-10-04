/**
 * @author USER
 */
var xmlHttpActi;


//DMDIAZF - Junio 14 de 2016
//Se cambia la función AJAX utilizada para la creacion de actividades economicas.
//LA funcion utilizada anteriormente presenta problemas de compatibilidad entre navegadores. 
//Se reemplaza la funcion utilizada por JQuery.
function grabaActi(codActi, empresa, inpid , periodo){
	var actividad = codActi;
	var numemp = empresa;
	var porcent = parseInt(document.getElementById(inpid).value);
	
	if ( (parseInt(porcent) <= 0) || (parseInt(porcent) > 100) ){
		alert("EL PORCENTAJE DE LA ACTIVIDAD DEBE SER MAYOR QUE 0 Y MENOR O IGUAL A 100");
		return false;
	}
	else{ 	
		//Lanzar ajax
		var url = "eas_insacti.php";
		if( parseInt( periodo ) == 2020 )
			url = "../eas_insacti.php";
		
		
		
		$.ajax({
	        type: "POST",
	        url: url,
	        data: {
	        	'nemp': numemp,
	        	'acti': actividad,
	        	'porce': porcent,
	        	'sid': Math.random()
	        },
	        dataType: "html",
	        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	        cache: false,
	        success: function (data) {
	        	if (data=="ERRORPORCE"){
	        		alert("La sumatoria de porcentajes de las actividades no es igual al 100%");
	        	}
	        	else{        		
	        		$("#cntacti").html(data);
	        		$("#cntlista").css("display","none");        		
	        	}
	        },
	        error: function (data) {
	        	alert("ERROR: " + data);
	        }
	    });
	}
	
}






/*********
var actividad;
function grabaActi(codActi, empresa, inpid)
{
	var actividad = codActi;
	var numemp = empresa;
	var porcent = parseInt(document.getElementById(inpid).value);
	
	if (parseInt(porcent) <= 0 || parseInt(porcent) > 100)
	{
		alert("EL PORCENTAJE DE LA ACTIVIDAD DEBE SER MAYOR QUE 0 Y MENOR O IGUAL A 100");
		return;
	}
	
	xmlHttpActi=creaActi();

	if (xmlHttpActi == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var url="eas_insacti.php";
	url=url+"?nemp="+numemp+"&acti="+actividad+"&porce="+porcent;
	url=url+"&sid="+Math.random();
	xmlHttpActi.onreadystatechange=estadoActi;
	xmlHttpActi.open("GET", url, true);
	xmlHttpActi.send(null);
	
} 

function estadoActi() 
{ 
	if (xmlHttpActi.readyState==4 || xmlHttpActi.readyState=="complete")
	{
		if (xmlHttpActi.responseText == "ERRORPORCE")
		{
			alert("SUMATORIA DE PORCENTAJES ACTIVIDADES NO ES IGUAL A 100")
		}
		else
		{
			document.getElementById("cntacti").innerHTML="";
			document.getElementById("cntacti").innerHTML=xmlHttpActi.responseText;
			document.getElementById(actividad).style.display = "none";
		}
	}
}
*********/


function creaActi()
{
	var xmlHttpActi = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpActi = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpActi = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpActi = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpActi;
}

var xmlCambio; 
function cambiActi(periodo, empresa)
{
	//alert ("aaalll");
	var nueva = document.getElementById("vnacti").value;
	var numemp = empresa;
	var peri = periodo;
	
	xmlCambio=creaActi();

	if (xmlCambio == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var url="cambioacti.php";
	url=url+"?empresa="+numemp+"&ciiu3="+nueva+"&periodo="+peri;
	url=url+"&sid="+Math.random();
	xmlCambio.onreadystatechange=estadoCambio;
	xmlCambio.open("GET", url, true);
	xmlCambio.send(null);
} 

function estadoCambio() 
{ 
	if (xmlCambio.readyState==4 || xmlCambio.readyState=="complete")
	{
		if (xmlCambio.responseText == "ERRORACTI")
		{
			alert("ACTIVIDAD NO EXISTE");
		}
		else
		{
			alert(xmlCambio.responseText);
			document.getElementById("nacti").style.display = "none";
		}
	}
}

var xmlDev; 
function devolucion(empresa, periodo)
{
	var numemp = empresa;
	var peri = periodo;
	
	xmlDev=creaActi();

	if (xmlDev == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var url="cambioacti.php";
	url=url+"?nemp="+numemp+"&control='SI'"+"&periodo="+peri;
	url=url+"&sid="+Math.random();
	xmlDev.onreadystatechange=estadoControl;
	xmlDev.open("GET", url, true);
	xmlDev.send(null);
}

function estadoControl() 
{ 
	if (xmlDev.readyState==4 || xmlDev.readyState=="complete")
	{
		alert(xmlDev.responseText);
	}
}
