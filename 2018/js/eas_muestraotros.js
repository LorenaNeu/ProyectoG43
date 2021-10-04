function muestraOtro(tipo)
{
	if (tipo == "oj")
	{
		if (document.getElementById("idlorg").value == 99.1)
		{
			document.getElementById("divoj").style.display = "block";
			document.getElementById("idnido").style.display = "block";
		}
	}
	
	if (tipo == "es")
	{
		if (document.getElementById("idestad").value == 7)
		{
			document.getElementById("dives").style.display = "block";
			document.getElementById("idnomeae").style.display = "block";
		}
	}
	
	if (tipo == "mes")
	{
		if (document.getElementById("idmesinf").value == 6)
		{
			document.getElementById("divmes").style.display = "block";
			document.getElementById("idmesmen").style.display = "block";
		}else{
			document.getElementById("divmes").style.display = "none";
			document.getElementById("idmesmen").style.display = "none";
			document.getElementById("idmesmen").value = "";
		}
	}
}
