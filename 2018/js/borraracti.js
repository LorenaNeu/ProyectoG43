/**
 * @author USER
 */
var xmlHttpBorra;


//DMDIAZF - Junio 14 de 2016
//Se cambia la función AJAX utilizada para eliminar las actividades economicas.
//La funcion utilizada anteriormente presenta problemas de compatibilidad entre navegadores. 
//Se reemplaza la funcion utilizada por JQuery.

function borraActi(codiAct, nemp, periodo){
	var actividad = codiAct;
	var numemp = nemp;	
	if ((actividad!='') && (nemp!='')){
		
		var url = "eas_borracti.php";
		if( parseInt( periodo ) == 2020 )
			url = "../eas_borracti.php";

		$.ajax({
	        type: "POST",
	        url: url,
	        data: {
	        	'nemp': numemp,
	        	'acti': actividad,	        	
	        	'sid': Math.random()
	        },
	        dataType: "html",
	        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	        cache: false,
	        success: function (data) {
	        	$("#cntacti").html(data);
	        },
	        error: function (data) {
	        	alert("ERROR: " + data);
	        }
	    });
		
	}
	
}

function creaBorra()
{
	var xmlHttpBorra = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpBorra = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpBorra = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpBorra = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpBorra;
}
