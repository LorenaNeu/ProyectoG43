var xmlHttpUsu;
function grabaDist(peri, numero)
{
	var periodo=peri;
	var nordemp = numero;
	var fecha = document.getElementById("idfecdist").value;
	xmlHttpDist=creaObjDist();

	if (xmlHttpDist==null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	
	resulta = chequear(fecha);
	if (resulta == "vacio")
	{
		alert("Debe diligenciar la FECHA DE DISTRIBUCION");
		return;
	}
	if (resulta == "AER")
	{
		alert("FECHA DE DISTRIBUCION INVALIDA");
		return;
	}
	
	var url="grabadist.php";
	var queryString = "periodo="+escape(periodo)+"&numero="+escape(nordemp)+"&fecha="+escape(fecha);

	xmlHttpDist.open("POST", url, true);
	xmlHttpDist.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpDist.onreadystatechange=estadoRetDist;
	xmlHttpDist.send(queryString);
}

function estadoRetDist()
{
	if (xmlHttpDist.readyState==4 || xmlHttpDist.readyState=="complete")
	{
		alert(xmlHttpDist.responseText);
	}
}

function creaObjDist()
{
	var xmlHttpUsu=null;
	try
	{
  // Firefox, Opera 8.0+, Safari
	xmlHttpUsu=new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
	{
    xmlHttpUsu=new ActiveXObject("Msxml2.XMLHTTP");
	}
  catch (e)
	{
	xmlHttpUsu=new ActiveXObject("Microsoft.XMLHTTP");
	}
	}
	return xmlHttpUsu;
}

function chequear(fechadist)
{
        var today = new Date()
	var annio = today.getFullYear();

	var retorno = "OK";
	if (fechadist.length == 0)
	{
		retorno = "vacio";
	}
	else
	{
		feccheck = fechadist.split("-");
		if (parseInt(feccheck[0]) != annio)
		{
			retorno = "AER";
		}
		if (parseInt(feccheck[1]) < 0 || parseInt(feccheck[1]) > 12)
		{
			retorno = "AER";
		}
		if (parseInt(feccheck[2]) < 0 || parseInt(feccheck[2]) > 31)
		{
			retorno = "AER";
		}
	}
	return retorno;
}
