/**
 * @author USER
 */
function abreTexto(idArea, idTexto)
{
	var estado = document.getElementById(idArea).style.display;
	if (estado == "block") {
		document.getElementById(idArea).style.display="none";
	}
	else {
		document.getElementById(idArea).style.display="block";
		document.getElementById(idTexto).focus();
	}
}

var xmlHttpObser;
var areaId;
var CodigoErr;
var codLinkJ;
var codLinkC;
function cierraTexto(empre, estab, mensa, modu, coderr, fuen, idtexto)
{
	var Empresa = empre;
	var Estable = estab;
	var Mensaje = mensa;
	var Modulo = modu;
	CodigoErr = coderr;
	codLinkJ = "j" + coderr;
	codLinkC = "c" + coderr;
	var Fuente = fuen;
	areaId = idtexto;
	var txtObser = document.getElementById(areaId).value;
	if (txtObser.length == 0) {
		alert("Observaci�n en blanco - INVALIDA");
		document.getElementById(CodigoErr).style.display="none";
		return;
	}
	
	xmlHttpObser = creaObjObs();
	var queyString = "";
	if (xmlHttpObser == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	
	var url = "eas_grabaobs.php";
	queryString = "numemp="+escape(Empresa)+"&numest="+escape(Estable)+"&mens="+escape(Mensaje)+"&modulo="+escape(Modulo)+"&coderr="+escape(CodigoErr)+"&fuente="+escape(Fuente)+"&obs="+escape(txtObser);
	xmlHttpObser.open("POST", url, true);
	xmlHttpObser.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpObser.setRequestHeader("Content-length", queryString.length);
	xmlHttpObser.onreadystatechange=estadoRetobs;
	xmlHttpObser.send(queryString);
	
}

function estadoRetobs() 
{ 
	if (xmlHttpObser.readyState==4 || xmlHttpObser.readyState=="complete")
	{
	    //alert(xmlHttpObser.responseText);
		document.getElementById(CodigoErr).style.display="none";
		if (document.getElementById(codLinkC) != null) {
			document.getElementById(codLinkC).style.display="none";
		}
		document.getElementById(codLinkJ).innerHTML="Observaciones";
		document.getElementById(codLinkJ).style.color="#347C17";
		location.reload();
	}
}

function corrije()
{
	document.getElementById("perso").disabled=false;
	document.getElementById("merror").style.display="none";
}

function muestraActi()
{
	document.getElementById("cntlista").style.display="block";
//	document.getElementById("listaacti").style.display="block";
}

function ocultar()
{
	document.getElementById("cntlista").style.display="none";
//	document.getElementById("titlista").style.display="none";
}

function creaObjObs()
{
	var xmlHttpObser = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpObser = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpObser = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpObser = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpObser;
}

function obseModi(capitulo)
{
	switch(capitulo)
	{
		case "costop":
			var valReal = parseInt(document.getElementById("idc3r11c1s").value) + parseInt(document.getElementById("idc3r11c2s").value) + parseInt(document.getElementById("idc3r11c3s").value);
			var valMax = parseInt(document.getElementById("idc3r11c1s").value) + parseInt(document.getElementById("idc3r11c2s").value) + parseInt(document.getElementById("idc3r11c3s").value) + 30;
			var valMin = parseInt(document.getElementById("idc3r11c1s").value) + parseInt(document.getElementById("idc3r11c2s").value) + parseInt(document.getElementById("idc3r11c3s").value) - 30;
			if (parseInt(document.getElementById("idc3r11c4s").value) < valMin || parseInt(document.getElementById("idc3r11c4s").value) > valMax)
			{
				alert("TOTAL CAP�TULO INCORRECTO");
				setTimeout("document.getElementById('idc3r10c3s').focus();", 10);
				return;
			}
			else
			{
				document.getElementById("idc3r11c4s").value = valReal;
			}
			break;
			
		case "otroscyg":
			var valReal = parseInt(document.getElementById("idc3r23c1").value) + parseInt(document.getElementById("idc3r23c2").value);
			var valMax = parseInt(document.getElementById("idc3r23c1").value) + parseInt(document.getElementById("idc3r23c2").value) + 37;
			var valMin = parseInt(document.getElementById("idc3r23c1").value) + parseInt(document.getElementById("idc3r23c2").value)  - 37;
			if (parseInt(document.getElementById("idc3r23c3").value) < valMin || parseInt(document.getElementById("idc3r23c3").value) > valMax)
			{
				alert("TOTAL CAP�TULO INCORRECTO");
				setTimeout("document.getElementById('idc3r22c2').focus();", 10);
				return;
			}
			else
			{
				document.getElementById("idc3r23c3").value = valReal;
			}
			break;
			
		case "activos":
			var totLinea = 0;
			var sumaLinea = ["idc7r14c1", "idc7r14c2", "idc7r14c3", "idc7r14c4", "idc7r14c5", "idc7r14c6"];
			for (i=0; i<6; i++)
			{
				totLinea = parseInt(totLinea) + parseInt(document.getElementById(sumaLinea[i]).value);
			}
			var valMax = totLinea + 63;
			var valMin = totLinea - 63;
			if (parseInt(document.getElementById("idc7r14c7").value) < valMin || parseInt(document.getElementById("idc7r14c7").value) > valMax)
			{
				alert("TOTAL CAP�TULO INCORRECTO");
				setTimeout("document.getElementById('idc7r13c6').focus();", 10);
				return;
			}
			else
			{
				document.getElementById("idc7r14c7").value = totLinea;
			}
			break;
			
		case "exist":
			var totCol1 = 0;
			var totCol2 = 0;
			var sumaCol1 = ["idc6r1c1", "idc6r2c1", "idc6r3c1", "idc6r4c1"];
			for (i=0; i<4; i++)
			{
				totCol1 = parseInt(totCol1) + parseInt(document.getElementById(sumaCol1[i]).value);
			}
			var valMax = totCol1 + 4;
			var valMin = totCol1 - 4;
			if (parseInt(document.getElementById("idc6r5c1").value) < valMin || parseInt(document.getElementById("idc6r5c1").value) > valMax)
			{
				alert("TOTAL COLUMNA 1 INCORRECTO");
				setTimeout("document.getElementById('idc6r4c1').focus();", 10);
				return;
			}
			else
			{
				document.getElementById("idc6r5c1").value = totCol1;
			}
			var sumaCol2 = ["idc6r1c3", "idc6r2c3", "idc6r3c3", "idc6r4c3"];
			for (i=0; i<4; i++)
			{
				totCol2 = parseInt(totCol2) + parseInt(document.getElementById(sumaCol2[i]).value);
			}
			var valMax = totCol2 + 4;
			var valMin = totCol2 - 4;
			if (parseInt(document.getElementById("idc6r5c3").value) < valMin || parseInt(document.getElementById("idc6r5c3").value) > valMax)
			{
				alert("TOTAL COLUMNA 2 INCORRECTO");
				setTimeout("document.getElementById('idc6r4c3').focus();", 10);
				return;
			}
			else
			{
				document.getElementById("idc6r5c3").value = totCol2;
			}
			break;
	}
	document.getElementById("textomod").value = "";
	if (document.getElementById("txtobsmodi").style.display == "block")
	{
		document.getElementById("txtobsmodi").style.display = "none";
	}
	else
	{
		document.getElementById("txtobsmodi").style.display = "block";
		document.getElementById("textomod").focus();
	}
}
