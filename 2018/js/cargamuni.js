/**
 * @author USER
 */
var xmlHttpMuni;
var listaMun;
var identLista;
var idContiene
function buscaMuni(codDep, idLmun, idlista, idCont)
{
	var codigoDep = codDep;
	identLista = idlista;
	listaMun = idLmun;
	idContiene = idCont;
	xmlHttpMuni=creaLista();

	if (xmlHttpMuni == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var url="eas_cargampio.php";
	url=url+"?codep="+escape(codigoDep)+"&ident="+escape(identLista)+"&nomlista="+escape(listaMun);
	url=url+"&sid="+Math.random();
	xmlHttpMuni.onreadystatechange=estadoLista;
	xmlHttpMuni.open("GET", url, true);
	xmlHttpMuni.send(null);
} 

function estadoLista() 
{ 
	if (xmlHttpMuni.readyState==4 || xmlHttpMuni.readyState=="complete")
	{
		document.getElementById(idContiene).innerHTML="";
		document.getElementById(idContiene).innerHTML=xmlHttpMuni.responseText;
	}
}

function creaLista()
{
	var xmlHttpMuni = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpMuni = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpMuni = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpMuni = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpMuni;
}