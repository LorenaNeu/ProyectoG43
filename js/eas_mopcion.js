/**
 * @author USER
 */
function muestraOpcion(tipo)
{
	switch(tipo)
	{
		case "cara":
			document.getElementById("txtdescrip").innerHTML="Actualizar informaci\u00F3n Car\u00E1tula \u00DAnica y Estructura de la Empresa";
			break;
		case  "poc":
			document.getElementById("txtdescrip").innerHTML="Personal Ocupado, Costos y gastos causados por el personal ocupado";
			break;
		case "oco":
			document.getElementById("txtdescrip").innerHTML="Otros Costos y gastos causados, Activos fijos, Inversiones";
			break;
		case "acti":
			document.getElementById("txtdescrip").innerHTML="Activos Fijos Tangibles";
			break;
		case "pm":
			document.getElementById("txtdescrip").innerHTML="Distribuci\u00F3n a Nivel Departamento";
			break;
		case "anex":
			document.getElementById("txtdescrip").innerHTML="TICS";
			break;
		case "usu":
			document.getElementById("txtdescrip").innerHTML="Mantenimiento y Consulta de Usuarios";
			break;
		case "for":
			document.getElementById("txtdescrip").innerHTML="Ingreso a revisi\u00F3n de formularios";
			break;
		case "oper":
			document.getElementById("txtdescrip").innerHTML="Consultar Estado del Operativo";
			break;
		case "mpp":
			document.getElementById("txtdescrip").innerHTML="Volver al Men\u00FA Principal";
			break;
		case "re":
			document.getElementById("txtdescrip").innerHTML="Ingreso a diligenciar el resumen empresarial";
			break;
		case "de":
			document.getElementById("txtdescrip").innerHTML="Seleccionar Establecimiento a diligenciar";
			break;
		case "resu":
			document.getElementById("txtdescrip").innerHTML="Resumen principales indicadores econ\u00F3micos del establecimiento";
			break;
		case "vg":
			document.getElementById("txtdescrip").innerHTML="An\u00E1lisis de Variables Generales";
			break;
		case "fdil":
			document.getElementById("txtdescrip").innerHTML="Formulario Diligenciado";
			break;
		case "br":
			document.getElementById("txtdescrip").innerHTML="Borrado de Empresas";
			break;		
		case "admon":
			document.getElementById("txtdescrip").innerHTML="Administraci&oacute;n";
			break;
		case "niif":
		    document.getElementById("txtdescrip").innerHTML="Normas Internacionales Financieras (NIIF)";
			break;
		case "ambi":  
		    document.getElementById("txtdescrip").innerHTML="Protecci&oacute;n y/o gesti&oacute;n ambiental";
			break;  
	}
}

function muestraTxt(idPara)
{
	var estado = document.getElementById(idPara).style.display;
	if (estado == "none") {
		document.getElementById(idPara).style.display = "block";
	}
	else {
		document.getElementById(idPara).style.display = "none";
	}
}

var mensajeObs;
var codigoObs;
var fuenteObs;
var moduloObs;
var xmlLeeObs;
var nomcnt;
var nomtxt;
var nombtn;

function muestraObs(empresa, mensaje, codigo, fuente, modulo)
{
	mensajeObs = mensaje;
	codigoObs = codigo;
	fuenteObs = fuente;
	moduloObs = modulo;
	idempre = empresa;
	periodoObs = periodo; 

	if (codigoObs == "DC")
	{
		nomcnt = "cntdc";
		nomtxt = "txtdc";
		nombtn = "btndc";
	}
	else 
	{
		nomcnt = "cntobs";
		nomtxt = "txtobser";
		nombtn = "btnobse";
	}
	if (document.getElementById(nomcnt).style.display == "block")
	{
		document.getElementById(nomcnt).style.display = "none";
	}
	else
	{
		document.getElementById(nomcnt).style.display = "block";
		document.getElementById(nomtxt).focus();
		xmlLeeObs = creaGobs();
		var queryString = "";

		if (xmlLeeObs == null)
		{
			alert ("El explorador no soporta solicitudes HTTP");
			return;
		}
		var url="eas_leeobs.php";
		queryString = "numemp="+escape(idempre)+"&modulo="+escape(moduloObs)+"&coderr="+escape(codigoObs)+"&fuente="+escape(fuenteObs);
		xmlLeeObs.open("POST", url, true);
		xmlLeeObs.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlLeeObs.onreadystatechange=estadoRetLobs;
		xmlLeeObs.send(queryString);
	}
}

function estadoRetLobs() 
{ 
	if (xmlLeeObs.readyState==4 || xmlLeeObs.readyState=="complete")
	{
		document.getElementById(nomtxt).innerHTML = xmlLeeObs.responseText;
	}
}

var xmlGobs;
function grabaObser(empresa, usuario)
{
	var idempre = empresa;
	var idusu = usuario;
	var observa = document.getElementById(nomtxt).value;
	xmlGobs = creaGobs();
	var queryString = "";

	if (xmlGobs == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}

	var url="eas_grabaobs.php";
	
	queryString = "numemp="+escape(idempre)+"&mens="+escape(mensajeObs)+"&modulo="+escape(moduloObs)+"&coderr="+escape(codigoObs)+"&fuente="+escape(fuenteObs)+"&obs="+escape(observa)+"&identu="+escape(idusu);
	xmlGobs.open("POST", url, true);
	xmlGobs.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlGobs.setRequestHeader("Content-length", queryString.length);
	xmlGobs.send(queryString);
	alert('Observaci\u00F3n almacenada satisfactoriamente');
	window.location.reload();
}

function estadoRetGobs() 
{ 
	if (xmlGobs.readyState==4 || xmlGobs.readyState=="complete")
	{
		document.getElementById(nomcnt).style.display="none";
		alert(xmlGobs.responseText);
	}
}

function creaGobs()
{
	var xmlHttpEnv = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpEnv = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpEnv = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpEnv = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpEnv;
}
