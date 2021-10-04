/**
 * @author USER
 */
function busCaratula()
{
	if (document.frmbusca.buscar[0].checked == true)
	{
		document.frmbusca.action = "eas_caratula.php";
	}
	else {
		if (document.frmbusca.buscar[1].checked == true)
		{
			document.frmbusca.action = "eas_rescaratula.php";
		}
	}
	return true;
}

function busCaratulaDir()
{
	if (document.frmbusca.buscar[0].checked == true)
	{
		document.frmbusca.action = "eas_caratulaDir.php";
	}
	else {
		if (document.frmbusca.buscar[1].checked == true)
		{
			document.frmbusca.action = "eas_rescaratula.php";
		}
	}
	return true;
}

function buscaFormu()
{
	document.frmbusca.action = "eas_buscar.php";
	return true;
}

function reloadope(region)
{
	var newloc = "eas_operativo.php?nreg="+region
	window.location = newloc;
}

function mActi()
{	
	document.getElementById("nacti").style.display = "block";	
	
}
