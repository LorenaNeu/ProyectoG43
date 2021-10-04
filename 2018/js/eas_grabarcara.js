/**
 * @author USER
 */
var xmlHttpGecara;
var tipoDoc = 0;
var numeroDoc = 0;


// Grabar la caratula de la EAS. Utiliza AJAX a eas_grabacara.php
function grabaCara(idEmpre, actividad, periodo){

	var anioact = periodo;
	var contadorActiv = 0;
	if (document.getElementById("contadorAct")){
		contadorActiv = document.getElementById("contadorAct").value;
	}
	else{
		contadorActiv = 0;
	}

	var resthot = ["5511", "5512", "5513", "5519", "5521", "5522", "5523", "5524", "5529", "5530"];
	var tya = ["6310", "6320", "6331", "6332", "6333", "6339", "6340","6390"];
	var cyc = ["6421", "6422", "6423", "6424", "6425", "6426", "6411", "6412"];
	var finan = ["7010", "7020", "7111", "7112", "7113", "7121", "7122", "7123", "7129", "7130", "7210", "7220", "7230", "7240", "7250", "7290", "7310", "7320", "7411", "7412", "7413", "7414", "7421", "7422", "7430", "7491", "7492", "7493", "7494", "7495", "7499"];
	var comuna = ["7921", "8050", "8511", "8512", "8513", "8514", "8515", "8519", "9212", "9213", "9214", "9219", "9220", "9301", "9302", "9303", "9309"];
	var Empresa = idEmpre;
	var queryString = "";

	xmlHttpGecara=creaObjCara();
	if (xmlHttpGecara == null){
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}

	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;

	//DMDIAZ - Validar los Radio Buttons de la pregunta Nro. 10  (Estos controles solo existen para el periodo 2012)
	var radBienes = document.getElementById("radBienes");
	var radServicios = document.getElementById("radServicios");
	var radBienesyServ = document.getElementById("radBienesyServ");
	var radNinguno = document.getElementById("radNinguno");

	if ((radBienes!=null)&&(radServicios!=null)&&(radBienesyServ!=null)&&(radNinguno!=null)){

		radBienes = document.getElementById("radBienes").checked;
		radServicios = document.getElementById("radServicios").checked;
		radBienesyServ = document.getElementById("radBienesyServ").checked;
		radNinguno = document.getElementById("radNinguno").checked;

		//Los controles existen y se ejecuta esta validacion en un periodo mayor a 2011
		if ((radBienes==false)&&(radServicios==false)&&(radBienesyServ==false)&&(radNinguno==false)){
			errores = "SI";
			mensaerr = mensaerr + "Debe indicar las operaciones de comercio exterior que realizo la empresa en el periodo.";
		}
		else{
			var opcomex = 4;
			if (radBienes==true){ opcomex = 1; }
			if (radServicios==true) { opcomex = 2; }
			if (radBienesyServ==true) { opcomex = 3; }
			if (radNinguno==true) { opcomex = 4; }
		}
	}
	else{
		//No existen los controles se est� ejecutando desde otro periodo.
		var opcomex = 0;
	}

	for (i=0; i<document.formcara.elements.length; i++){
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar){
			case "idtipodo":	if (document.formcara.elements[i].checked == true){
									tipoDoc = valorcheck;
								}
								break;

			case "idnitcc":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el N\xDAMERO DE DOCUMENTO\n";
								}
								numeroDoc = valorcheck;
								break;

			case "iddv":		if (tipoDoc == 1){
									resdigi = checkDigi(numeroDoc);
									if (resdigi != valorcheck){
										mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
										errores = "SI";
									}
								}
								break;

			case "idproraz":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la RAZ\xD3N SOCIAL\n";
								}
								break;

			case "idnomcom":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
								}
								break;

			case "iddirecc":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N\n";
								}
								break;

			case "idtel":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO\n";
								}
								break;

			case "dirn":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N DE NOTIFICACI\xD3N\n";
								}
								break;

			case "idtelno":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO DE NOTIFICACI\xD3N\n";
								}
								break;

			case "idfcd":		var fechaIniA = valorcheck;
								var fechaIniB = fechaIniA.replace("-", "/");
								resulta = checkFechas(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI\xD3N\n";
								}
								if (resulta == "AER"){
									errores = "SI";
									mensaerr = mensaerr+"FECHA DE CONSTITUCI\xD3N [DESDE] INVALIDA\n";
								}
								break;

			case "idfch":		var fechaFinA = valorcheck;
								var fechaFinB = fechaFinA.replace("-", "/");
								resulta = checkFechah(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI\xD3N [HASTA]\n";
									mensaerr = mensaerr+"Si NO tiene esta fecha entre [0000-00-00]\n";
								}
								if (resulta == "AER"){
									errores = "SI";
									mensaerr = mensaerr+"FECHA DE CONSTITUCI\xD3N [HASTA] INVALIDA\n";
								}
								if (fechaFinA != "0000-00-00"){
									if (Date.parse(fechaIniB) > Date.parse(fechaFinB)){
										errores = "SI";
										mensaerr = mensaerr+"Fecha de constituci\xF3n DESDE no puede ser mayor que fecha HASTA";
									}
								}
								break;

			case "ccsprn":		resulta = checkCapital(nombrevar, valorcheck);
								if (resulta == "ERR"){
									errores = "SI";
									mensaerr = mensaerr+"La sumatoria de los componentes del CAPITAL SOCIAL debe ser igual a 100%\n";
								}
								break;

			case "estind":		resulta = checkEstind(nombrevar, valorcheck);
								if (resulta == "ERR"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar Numero de ESTABLECIMIENTOS MANUFACTUREROS\n";
								}
								break;

			case "responde":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar PERSONA QUE DILIGENCIA\n";
								}
								break;

			case "teler":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar TEL\xC9FONO DE PERSONA QUE DILIGENCIA\n";
								}
								break;

			case "idaio":		if  (parseInt(valorcheck) > anioact || parseInt(valorcheck) < 1800){
									errores = "SI";
									mensaerr = mensaerr+"A\xD1O INICIO DE OPERACIONES INVALIDO\n";
								}

				 				//arreglo fecha inicion operaciones
				 				//@author Jonathan Esquivel <jresquivelf@dane.gov.co>
				 				//@since 13/04/2012
				 				var str = document.getElementById("idfechai").value;
				 				var anio = parseInt(str.split("-",1));
				 				if(parseInt(valorcheck) < anio){
				 					errores = "SI";
				 					mensaerr = mensaerr+"A\xD1O INICIO DE OPERACIONES ES ANTERIOR A LA FECHA DE CONSTITUCI\xD3N\n";
				 				}
				 				break;

			case "idmeop":		if  (parseInt(valorcheck) > 12 || parseInt(valorcheck) < 0){
									errores = "SI";
									mensaerr = mensaerr+"MESES DE OPERACI\xD3N INVALIDO\n";
								}
								break;

			case "AUGENE":      var augene = document.getElementById("AUGENE").options[document.getElementById("AUGENE").selectedIndex].value;
						        if (augene=='-'){
						        	errores = "SI";
						        	mensaerr = mensaerr + "DEBE INDICAR EL VALOR DE GENERACION DE ENERGIA POR AUTOGENERACION\n";
						        }
								break;

			case "COGENE": 		var cogene = document.getElementById("COGENE").options[document.getElementById("COGENE").selectedIndex].value;
	        					if (cogene=='-'){
	        						errores = "SI";
	        						mensaerr = mensaerr + "DEBE INDICAR EL VALOR DE GENERACION DE ENERGIA POR COGENERACION\n";
	        					}
            					break;

			case "idnuls":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el numero de departamentos donde la empresa cuenta con establecimientos de servicios";
								}

								var idnuls = document.getElementById("idnuls").value;
								if(idnuls == 0){
									errores = "SI";
									mensaerr = mensaerr+"El numero de departamentos donde la empresa cuenta con establecimientos de servicios debe ser mayor a 0";
								}
								break;
		}
	}


	var sigue = 1;
	for (i=0; i<resthot.length; i++){
		if (resthot[i] == actividad){
			sigue = 0;
			if (document.getElementById("idresh").value == 0){
				errores = "SI";
				mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE RESTAURANTES Y HOTELES\n";
			}
			break;
		}
	}


	if (sigue == 1){
		for (i=0; i<tya.length; i++){
			if (tya[i] == actividad){
				sigue = 0;
				if (document.getElementById("idtya").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE TRANSPORTE Y ALMACENAMIENTO\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<cyc.length; i++){
			if (cyc[i] == actividad){
				sigue = 0;
				if (document.getElementById("idcyc").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE COMUNICACIONES Y CORREO\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<finan.length; i++){
			if (finan[i] == actividad){
				sigue = 0;
				if (document.getElementById("idfin").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE SERVICIOS FINANCIEROS\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<comuna.length; i++){
			if (comuna[i] == actividad){
				sigue = 0;
				if (document.getElementById("idserc").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE SERVICIOS COMUNALES\n";
				}
				break;
			}
		}
	}





	//Preguntar si ya se adicionaron actividades en el campo de adicionar empresa.
	if (contadorActiv==0){
		errores = "SI";
		mensaerr = mensaerr + "DEBE ADICIONAR ACTIVIDADES - ACTIVIDAD ECONOMICA DE LA EMPRESA\n";
	}

	//Validar que las actividades economicas que se agregan sean iguales al 100%
	if (contadorActiv > 0){
		var porcentajeActividades = 0;
		var elements = document.getElementsByName("hddActi");
		for (x=0; x<elements.length; x++){
			porcentajeActividades = porcentajeActividades + parseInt(elements[x].getAttribute("value"));
		}

		if (porcentajeActividades<100){
			errores = "SI";
			mensaerr = "EL PORCENTAJE TOTAL DE LAS ACTIVIDADES INGRESADAS DEBE SER IGUAL AL 100 %\n";
		}
	}


	if (errores == "SI"){
		alert(mensaerr);
		return false;
	}

	//Por medio de funcion jquery AJAX, se almacena la informacion de la tabla de generacion de energia si el
	//periodo es mayor o igual al 2016

	var totalPlantas = parseInt($("#NPLANTE").val()) + parseInt($("#NPLANTR").val()) + parseInt($("#NPLANTS").val()) + parseInt($("#NPLANTEO").val()) + parseInt($("#NPLANTO").val());

	if (parseInt(anioact) >= 2016){

			var error = false;

			//Realizar las validaciones del formulario de generacion de energia

			//No puede ir ningun campo en blanco.

			if ($("#NPLANTE").val()==""){
				alert("Debe indicar la cantidad de plantas de emergencia que posee.");
				error = true;
			}
			else if ($("#NPLANTR").val()==""){
				alert("Debe indicar la cantidad de plantas de respaldo que posee.");
				error = true;
			}
			else if ($("#NPLANTS").val()==""){
				alert("Debe indicar la cantidad de plantas solares que posee.");
				error = true;
			}
			else if ($("#NPLANTEO").val()==""){
				alert("Debe indicar la cantidad de plantas eolicas que posee.");
				error = true;
			}
			else if ($("#NPLANTO").val()==""){
				alert("Debe indicar la cantidad de plantas de otro tipo que posee.");
				error = true;
			}
			else if ((parseInt($("#NPLANTE").val()) > 0 ) && (!$("#radEme1").is(":checked") && !$("#radEme2").is(":checked") && !$("#radEme3").is(":checked")) ){
					alert("Debe seleccionar el tipo de combustible que utliza la planta de emergencia");
					error = true;
			}
			else if ( (parseInt($("#NPLANTR").val()) > 0) && !$("#radResp1").is(":checked") && !$("#radResp2").is(":checked") && !$("#radResp3").is(":checked")){
					alert("Debe seleccionar el tipo de combustible que utliza la planta de respaldo");
					error = true;
			}
			else if (parseInt($("#NPLANTE").val()) > 0    && ($("#CKWPE").val()=='' || parseInt($("#CKWPE").val())==0)){
				alert("Debe indicar el valor total de la capacidad en kW (3) de la planta de emergencia");
				$("#CKWPE").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTE").val()) > 0    && ($("#TTPLANE").val()=='' || parseInt($("#TTPLANE").val())==0)){
				alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta de emergencia");
				$("#TTPLANE").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTE").val()) > 0 && !parseInt($("#CPLANTEG").val())>0 && !parseInt($("#CPLANTED").val())>0 && !parseInt($("#CPLANTEO").val())>0){
				alert("Debe indicar la cantidad (Galones/a\u00f1o) de combustible con el que funciona la planta(5) de emergencia");
				$("#CPLANTEG").focus();
				error = true;
			}
			else if (parseInt($("#CPLANTEO").val()) > 0 && $("#EMCUAL").val()==''){
				alert("Por favor indique con cual tipo de combustible funciona la planta de emergencia");
				$("#EMCUAL").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTR").val()) > 0    && ($("#CKWPR").val()=='' || parseInt($("#CKWPR").val())==0)){
				alert("Debe indicar el valor total de la capacidad en kW (3) de la planta de respaldo");
				$("#CKWPR").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTR").val()) > 0    && ($("#TTPLANR").val()=='' || parseInt($("#TTPLANR").val())==0)){
				alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta de respaldo");
				$("#TTPLANR").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTR").val()) > 0 && !parseInt($("#CPLANTRG").val())>0 && !parseInt($("#CPLANTRD").val())>0 && !parseInt($("#CPLANTRO").val())>0){
				alert("Debe indicar la cantidad (Galones/a\u00f1o) de combustible con el que funciona la planta(5) de respaldo");
				$("#CPLANTRG").focus();
				error = true;
			}
			else if (parseInt($("#CPLANTRO").val()) > 0 && $("#RESCUAL").val()==''){
				alert("Por favor indique el tipo de combustible con el que funciona la planta de respaldo");
				$("#RESCUAL").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTS").val()) > 0    && ($("#CKWPS").val()=='' || parseInt($("#CKWPS").val())==0)){
				alert("Debe indicar el valor total de la capacidad en kW (3) de la planta solar");
				$("#CKWPS").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTS").val()) > 0    && ($("#TTPLANS").val()=='' || parseInt($("#TTPLANS").val())==0)){
				alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta solar");
				$("#TTPLANS").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTEO").val()) > 0    && ($("#CKWPEO").val()=='' || parseInt($("#CKWPEO").val())==0)){
				alert("Debe indicar el valor total de la capacidad en kW (3) de la planta e\u00f3lica");
				$("#CKWPEO").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTEO").val()) > 0    && ($("#TTPLANEO").val()=='' || parseInt($("#TTPLANEO").val())==0)){
				alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta e\u00f3lica");
				$("#TTPLANEO").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTO").val()) > 0    && ($("#CKWPO").val()=='' || parseInt($("#CKWPO").val())==0)){
				alert("Debe indicar el valor total de la capacidad en kW (3) de la planta (otra)");
				$("#CKWPO").focus();
				error = true;
			}
			else if (parseInt($("#NPLANTO").val()) > 0    && ($("#TTPLANO").val()=='' || parseInt($("#TTPLANO").val())==0)){
				alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta (otra)");
				$("#TTPLANO").focus();
				error = true;
			}
			else if (parseInt($("#TTPLANO").val()) > 0 && $("#OTRCUAL").val()==''){
				alert("Por favor indique el otro tipo de planta que utiliza.");
				$("#OTRCUAL").focus();
				error = true;
			}
			else if (parseInt($("#TTPLANE").val()) > 100){
				alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas de emergencia no debe ser superior a 100.");
				$("#TTPLANE").focus();
				error = true;
			}
			else if ((parseInt($("#NPLANTR").val()) > 0) && (parseInt($("#TTPLANR").val()) > (parseInt($("#NPLANTR").val()) * 8760))){
				alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas de de respaldo no debe ser superior a NPLANTR * 8760 horas.");
				$("#TTPLANR").val();
				error = true;
			}
			else if ((parseInt($("#NPLANTS").val()) > 0) && (parseInt($("#TTPLANS").val()) > (parseInt($("#NPLANTS").val()) * 8760))){
				alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas solares no debe ser superior a NPLANTS * 8760 horas.");
				$("#TTPLANS").val();
				error = true;
			}
			else if ((parseInt($("#NPLANTEO").val()) > 0) && (parseInt($("#TTPLANEO").val()) > (parseInt($("#NPLANTEO").val()) * 8760))){
				alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas e\u00f3licas no debe ser superior a NPLANTEO * 8760 horas.");
				$("#TTPLANEO").val();
				error = true;
			}
			else if ((parseInt($("#NPLANTO").val()) > 0) && (parseInt($("#TTPLANO").val()) > (parseInt($("#NPLANTO").val()) * 8760))){
				alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas otra  no debe ser superior a NPLANTO * 8760 horas.");
				$("#TTPLANO").val();
				error = true;
			}

			else{
				//Validar que la sumatoria de las capacidades de las plantas no supere los 300.000 kW
				if ((parseInt($("#CKWPE").val()) + parseInt($("#CKWPR").val()) + parseInt($("#CKWPS").val()) + parseInt($("#CKWPEO").val()) + parseInt($("#CKWPO").val())) > 300000){
					alert("La capacidad total de las plantas de energ\u00eda no puede superar los 300.000 (kW)");
					$("#CKWPE").focus();
					error = true;
				}
				else{
					//Guardar la informacion del formulario de generacion de energia.
					guardarInfoGeneracionEnergia(periodo, idEmpre);
				}
			}

			if (!error){
				//Guardar el formulario
				document.getElementById("idgrabacara").disabled = true;
				var url = "eas_grabacara.php";
				//alert( parseInt( periodo ) );
				if( parseInt( periodo ) == 2020 )
					url = "eas_caratula_registra.php";

				//alert( url );

				queryString = "numemp="+escape(Empresa);
				for (i=0; i<document.formcara.elements.length; i++){
					if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3"){
						if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked){
							queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
						}
						else if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button"){
							queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
						}
					}
				}
				queryString = queryString + "&opcomex=" + opcomex;
				xmlHttpGecara.open("POST", url, true);
				xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xmlHttpGecara.onreadystatechange=estadoRetCara;
				xmlHttpGecara.send(queryString);
			}

	}
	else{
		//Guardar el formulario para periodos menores al 2016
		document.getElementById("idgrabacara").disabled = true;
		var url = "eas_grabacara.php";
		if( parseInt( periodo ) == 2020 )
			url = "eas_caratula_registra.php";
		queryString = "numemp="+escape(Empresa);
		for (i=0; i<document.formcara.elements.length; i++){
			if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3"){
				if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked){
					queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
				}
				else if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button"){
					queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
				}
			}
		}
		queryString = queryString + "&opcomex=" + opcomex;
		xmlHttpGecara.open("POST", url, true);
		xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlHttpGecara.onreadystatechange=estadoRetCara;
		xmlHttpGecara.send(queryString);
	}
}




	// Grabar la caratula de la EAS. Utiliza AJAX a eas_grabacara.php
function grabaCara2020(idEmpre, actividad, periodo){

	var anioact = periodo;
	var contadorActiv = 0;
	if (document.getElementById("contadorAct")){
		contadorActiv = document.getElementById("contadorAct").value;
	}
	else{
		contadorActiv = 0;
	}

	var resthot = ["5511", "5512", "5513", "5519", "5521", "5522", "5523", "5524", "5529", "5530"];
	var tya = ["6310", "6320", "6331", "6332", "6333", "6339", "6340","6390"];
	var cyc = ["6421", "6422", "6423", "6424", "6425", "6426", "6411", "6412"];
	var finan = ["7010", "7020", "7111", "7112", "7113", "7121", "7122", "7123", "7129", "7130", "7210", "7220", "7230", "7240", "7250", "7290", "7310", "7320", "7411", "7412", "7413", "7414", "7421", "7422", "7430", "7491", "7492", "7493", "7494", "7495", "7499"];
	var comuna = ["7921", "8050", "8511", "8512", "8513", "8514", "8515", "8519", "9212", "9213", "9214", "9219", "9220", "9301", "9302", "9303", "9309"];
	var Empresa = idEmpre;
	var queryString = "";

	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;

	//DMDIAZ - Validar los Radio Buttons de la pregunta Nro. 10  (Estos controles solo existen para el periodo 2012)
	var radBienes = document.getElementById("radBienes");
	var radServicios = document.getElementById("radServicios");
	var radBienesyServ = document.getElementById("radBienesyServ");
	var radNinguno = document.getElementById("radNinguno");

	if ((radBienes!=null)&&(radServicios!=null)&&(radBienesyServ!=null)&&(radNinguno!=null)){

		radBienes = document.getElementById("radBienes").checked;
		radServicios = document.getElementById("radServicios").checked;
		radBienesyServ = document.getElementById("radBienesyServ").checked;
		radNinguno = document.getElementById("radNinguno").checked;

		//Los controles existen y se ejecuta esta validacion en un periodo mayor a 2011
		if ((radBienes==false)&&(radServicios==false)&&(radBienesyServ==false)&&(radNinguno==false)){
			errores = "SI";
			mensaerr = mensaerr + "Debe indicar las operaciones de comercio exterior que realizo la empresa en el periodo.";
		}
		else{
			var opcomex = 4;
			if (radBienes==true){ opcomex = 1; }
			if (radServicios==true) { opcomex = 2; }
			if (radBienesyServ==true) { opcomex = 3; }
			if (radNinguno==true) { opcomex = 4; }
		}
	}
	else{
		//No existen los controles se est� ejecutando desde otro periodo.
		var opcomex = 0;
	}

	for (i=0; i<document.formcara.elements.length; i++){
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar){
			case "idtipodo":	if (document.formcara.elements[i].checked == true){
									tipoDoc = valorcheck;
								}
								break;

			case "idnitcc":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el N\xDAMERO DE DOCUMENTO\n";
								}
								numeroDoc = valorcheck;
								break;

			case "iddv":		if (tipoDoc == 1){
									resdigi = checkDigi(numeroDoc);
									if (resdigi != valorcheck){
										mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
										errores = "SI";
									}
								}
								break;

			case "idproraz":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la RAZ\xD3N SOCIAL\n";
								}
								break;

			case "idnomcom":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
								}
								break;

			case "iddirecc":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N\n";
								}
								break;

			case "idtel":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO\n";
								}
								break;

			case "dirn":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N DE NOTIFICACI\xD3N\n";
								}
								break;

			case "idtelno":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO DE NOTIFICACI\xD3N\n";
								}
								break;

			case "idfcd":		var fechaIniA = valorcheck;
								var fechaIniB = fechaIniA.replace("-", "/");
								resulta = checkFechas(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI\xD3N\n";
								}
								if (resulta == "AER"){
									errores = "SI";
									mensaerr = mensaerr+"FECHA DE CONSTITUCI\xD3N [DESDE] INVALIDA\n";
								}
								break;

			case "idfch":		var fechaFinA = valorcheck;
								var fechaFinB = fechaFinA.replace("-", "/");
								resulta = checkFechah(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI\xD3N [HASTA]\n";
									mensaerr = mensaerr+"Si NO tiene esta fecha entre [0000-00-00]\n";
								}
								if (resulta == "AER"){
									errores = "SI";
									mensaerr = mensaerr+"FECHA DE CONSTITUCI\xD3N [HASTA] INVALIDA\n";
								}
								if (fechaFinA != "0000-00-00"){
									if (Date.parse(fechaIniB) > Date.parse(fechaFinB)){
										errores = "SI";
										mensaerr = mensaerr+"Fecha de constituci\xF3n DESDE no puede ser mayor que fecha HASTA";
									}
								}
								break;

			case "ccsprn":		resulta = checkCapital(nombrevar, valorcheck);
								if (resulta == "ERR"){
									errores = "SI";
									mensaerr = mensaerr+"La sumatoria de los componentes del CAPITAL SOCIAL debe ser igual a 100%\n";
								}
								break;

			case "estind":		resulta = checkEstind(nombrevar, valorcheck);
								if (resulta == "ERR"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar Numero de ESTABLECIMIENTOS MANUFACTUREROS\n";
								}
								break;

			case "responde":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar PERSONA QUE DILIGENCIA\n";
								}
								break;

			case "emailr":	resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar correo electronico\n";
								}
								break;

			case "teler":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar TEL\xC9FONO DE PERSONA QUE DILIGENCIA\n";
								}
								break;

			case "idaio":		if  (parseInt(valorcheck) > anioact || parseInt(valorcheck) < 1800){
									errores = "SI";
									mensaerr = mensaerr+"A\xD1O INICIO DE OPERACIONES INVALIDO\n";
								}

				 				//arreglo fecha inicion operaciones
				 				//@author Jonathan Esquivel <jresquivelf@dane.gov.co>
				 				//@since 13/04/2012
				 				var str = document.getElementById("idfechai").value;
				 				var anio = parseInt(str.split("-",1));
				 				if(parseInt(valorcheck) < anio){
				 					errores = "SI";
				 					mensaerr = mensaerr+"A\xD1O INICIO DE OPERACIONES ES ANTERIOR A LA FECHA DE CONSTITUCI\xD3N\n";
				 				}
				 				break;

			case "idmeop":		if  (parseInt(valorcheck) > 12 || parseInt(valorcheck) < 0){
									errores = "SI";
									mensaerr = mensaerr+"MESES DE OPERACI\xD3N INVALIDO\n";
								}
								break;

			case "AUGENE":      var augene = document.getElementById("AUGENE").options[document.getElementById("AUGENE").selectedIndex].value;
						        if (augene=='-'){
						        	errores = "SI";
						        	mensaerr = mensaerr + "DEBE INDICAR EL VALOR DE GENERACION DE ENERGIA POR AUTOGENERACION\n";
						        }
								break;

			case "COGENE": 		var cogene = document.getElementById("COGENE").options[document.getElementById("COGENE").selectedIndex].value;
	        					if (cogene=='-'){
	        						errores = "SI";
	        						mensaerr = mensaerr + "DEBE INDICAR EL VALOR DE GENERACION DE ENERGIA POR COGENERACION\n";
	        					}
            					break;

			case "idnuls":		resulta = checkBlanco(nombrevar, valorcheck);
								if (resulta == "vacio"){
									errores = "SI";
									mensaerr = mensaerr+"Debe diligenciar el numero de departamentos donde la empresa cuenta con establecimientos de servicios";
								}

								var idnuls = document.getElementById("idnuls").value;
								if(idnuls == 0){
									errores = "SI";
									mensaerr = mensaerr+"El numero de departamentos donde la empresa cuenta con establecimientos de servicios debe ser mayor a 0";
								}
								break;
		}
	}


	var sigue = 1;
	for (i=0; i<resthot.length; i++){
		if (resthot[i] == actividad){
			sigue = 0;
			if (document.getElementById("idresh").value == 0){
				errores = "SI";
				mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE RESTAURANTES Y HOTELES\n";
			}
			break;
		}
	}


	if (sigue == 1){
		for (i=0; i<tya.length; i++){
			if (tya[i] == actividad){
				sigue = 0;
				if (document.getElementById("idtya").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE TRANSPORTE Y ALMACENAMIENTO\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<cyc.length; i++){
			if (cyc[i] == actividad){
				sigue = 0;
				if (document.getElementById("idcyc").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE COMUNICACIONES Y CORREO\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<finan.length; i++){
			if (finan[i] == actividad){
				sigue = 0;
				if (document.getElementById("idfin").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE SERVICIOS FINANCIEROS\n";
				}
				break;
			}
		}
	}

	if (sigue == 1){
		for (i=0; i<comuna.length; i++){
			if (comuna[i] == actividad){
				sigue = 0;
				if (document.getElementById("idserc").value == 0){
					errores = "SI";
					mensaerr = mensaerr+"DEBE REPORTAR ESTABLECIMIENTOS DE SERVICIOS COMUNALES\n";
				}
				break;
			}
		}
	}





	//Preguntar si ya se adicionaron actividades en el campo de adicionar empresa.
	if (contadorActiv==0){
		errores = "SI";
		mensaerr = mensaerr + "DEBE ADICIONAR ACTIVIDADES - ACTIVIDAD ECONOMICA DE LA EMPRESA\n";
	}

	//Validar que las actividades economicas que se agregan sean iguales al 100%
	if (contadorActiv > 0){
		var porcentajeActividades = 0;
		var elements = document.getElementsByName("hddActi");
		for (x=0; x<elements.length; x++){
			porcentajeActividades = porcentajeActividades + parseInt(elements[x].getAttribute("value"));
		}

		if (porcentajeActividades<100){
			errores = "SI";
			mensaerr = "EL PORCENTAJE TOTAL DE LAS ACTIVIDADES INGRESADAS DEBE SER IGUAL AL 100 %\n";
		}
	}


	if (errores == "SI"){
		alert(mensaerr);
		return false;
	}

	//Por medio de funcion jquery AJAX, se almacena la informacion de la tabla de generacion de energia si el
	//periodo es mayor o igual al 2016

	var totalPlantas = parseInt($("#NPLANTE").val()) + parseInt($("#NPLANTR").val()) + parseInt($("#NPLANTS").val()) + parseInt($("#NPLANTEO").val()) + parseInt($("#NPLANTO").val());


		var error = false;

		//Realizar las validaciones del formulario de generacion de energia

		//No puede ir ningun campo en blanco.

		if ($("#NPLANTE").val()==""){
			alert("Debe indicar la cantidad de plantas de emergencia que posee.");
			error = true;
		}
		else if ($("#NPLANTR").val()==""){
			alert("Debe indicar la cantidad de plantas de respaldo que posee.");
			error = true;
		}
		else if ($("#NPLANTS").val()==""){
			alert("Debe indicar la cantidad de plantas solares que posee.");
			error = true;
		}
		else if ($("#NPLANTEO").val()==""){
			alert("Debe indicar la cantidad de plantas eolicas que posee.");
			error = true;
		}
		else if ($("#NPLANTO").val()==""){
			alert("Debe indicar la cantidad de plantas de otro tipo que posee.");
			error = true;
		}
		else if ((parseInt($("#NPLANTE").val()) > 0 ) && (!$("#radEme1").is(":checked") && !$("#radEme2").is(":checked") && !$("#radEme3").is(":checked")) ){
				alert("Debe seleccionar el tipo de combustible que utliza la planta de emergencia");
				error = true;
		}
		else if ( (parseInt($("#NPLANTR").val()) > 0) && !$("#radResp1").is(":checked") && !$("#radResp2").is(":checked") && !$("#radResp3").is(":checked")){
				alert("Debe seleccionar el tipo de combustible que utliza la planta de respaldo");
				error = true;
		}
		else if (parseInt($("#NPLANTE").val()) > 0    && ($("#CKWPE").val()=='' || parseInt($("#CKWPE").val())==0)){
			alert("Debe indicar el valor total de la capacidad en kW (3) de la planta de emergencia");
			$("#CKWPE").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTE").val()) > 0    && ($("#TTPLANE").val()=='' || parseInt($("#TTPLANE").val())==0)){
			alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta de emergencia");
			$("#TTPLANE").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTE").val()) > 0 && !parseInt($("#CPLANTEG").val())>0 && !parseInt($("#CPLANTED").val())>0 && !parseInt($("#CPLANTEO").val())>0){
			alert("Debe indicar la cantidad (Galones/a\u00f1o) de combustible con el que funciona la planta(5) de emergencia");
			$("#CPLANTEG").focus();
			error = true;
		}
		else if (parseInt($("#CPLANTEO").val()) > 0 && $("#EMCUAL").val()==''){
			alert("Por favor indique con cual tipo de combustible funciona la planta de emergencia");
			$("#EMCUAL").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTR").val()) > 0    && ($("#CKWPR").val()=='' || parseInt($("#CKWPR").val())==0)){
			alert("Debe indicar el valor total de la capacidad en kW (3) de la planta de respaldo");
			$("#CKWPR").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTR").val()) > 0    && ($("#TTPLANR").val()=='' || parseInt($("#TTPLANR").val())==0)){
			alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta de respaldo");
			$("#TTPLANR").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTR").val()) > 0 && !parseInt($("#CPLANTRG").val())>0 && !parseInt($("#CPLANTRD").val())>0 && !parseInt($("#CPLANTRO").val())>0){
			alert("Debe indicar la cantidad (Galones/a\u00f1o) de combustible con el que funciona la planta(5) de respaldo");
			$("#CPLANTRG").focus();
			error = true;
		}
		else if (parseInt($("#CPLANTRO").val()) > 0 && $("#RESCUAL").val()==''){
			alert("Por favor indique el tipo de combustible con el que funciona la planta de respaldo");
			$("#RESCUAL").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTS").val()) > 0    && ($("#CKWPS").val()=='' || parseInt($("#CKWPS").val())==0)){
			alert("Debe indicar el valor total de la capacidad en kW (3) de la planta solar");
			$("#CKWPS").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTS").val()) > 0    && ($("#TTPLANS").val()=='' || parseInt($("#TTPLANS").val())==0)){
			alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta solar");
			$("#TTPLANS").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTEO").val()) > 0    && ($("#CKWPEO").val()=='' || parseInt($("#CKWPEO").val())==0)){
			alert("Debe indicar el valor total de la capacidad en kW (3) de la planta e\u00f3lica");
			$("#CKWPEO").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTEO").val()) > 0    && ($("#TTPLANEO").val()=='' || parseInt($("#TTPLANEO").val())==0)){
			alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta e\u00f3lica");
			$("#TTPLANEO").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTO").val()) > 0    && ($("#CKWPO").val()=='' || parseInt($("#CKWPO").val())==0)){
			alert("Debe indicar el valor total de la capacidad en kW (3) de la planta (otra)");
			$("#CKWPO").focus();
			error = true;
		}
		else if (parseInt($("#NPLANTO").val()) > 0    && ($("#TTPLANO").val()=='' || parseInt($("#TTPLANO").val())==0)){
			alert("Debe indicar el tiempo total de utilizaci\u00f3n (horas/a\u00f1o) (4) de la planta (otra)");
			$("#TTPLANO").focus();
			error = true;
		}
		else if (parseInt($("#TTPLANO").val()) > 0 && $("#OTRCUAL").val()==''){
			alert("Por favor indique el otro tipo de planta que utiliza.");
			$("#OTRCUAL").focus();
			error = true;
		}
		else if (parseInt($("#TTPLANE").val()) > 100){
			alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas de emergencia no debe ser superior a 100.");
			$("#TTPLANE").focus();
			error = true;
		}
		else if ((parseInt($("#NPLANTR").val()) > 0) && (parseInt($("#TTPLANR").val()) > (parseInt($("#NPLANTR").val()) * 8760))){
			alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas de de respaldo no debe ser superior a NPLANTR * 8760 horas.");
			$("#TTPLANR").val();
			error = true;
		}
		else if ((parseInt($("#NPLANTS").val()) > 0) && (parseInt($("#TTPLANS").val()) > (parseInt($("#NPLANTS").val()) * 8760))){
			alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas solares no debe ser superior a NPLANTS * 8760 horas.");
			$("#TTPLANS").val();
			error = true;
		}
		else if ((parseInt($("#NPLANTEO").val()) > 0) && (parseInt($("#TTPLANEO").val()) > (parseInt($("#NPLANTEO").val()) * 8760))){
			alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas e\u00f3licas no debe ser superior a NPLANTEO * 8760 horas.");
			$("#TTPLANEO").val();
			error = true;
		}
		else if ((parseInt($("#NPLANTO").val()) > 0) && (parseInt($("#TTPLANO").val()) > (parseInt($("#NPLANTO").val()) * 8760))){
			alert("El tiempo de utilizaci\u00f3n (horas/a\u00f1o) de las plantas otra  no debe ser superior a NPLANTO * 8760 horas.");
			$("#TTPLANO").val();
			error = true;
		}

		else{
			//Validar que la sumatoria de las capacidades de las plantas no supere los 300.000 kW
			if ((parseInt($("#CKWPE").val()) + parseInt($("#CKWPR").val()) + parseInt($("#CKWPS").val()) + parseInt($("#CKWPEO").val()) + parseInt($("#CKWPO").val())) > 300000){
				alert("La capacidad total de las plantas de energ\u00eda no puede superar los 300.000 (kW)");
				$("#CKWPE").focus();
				error = true;
			}
			else{
				//Guardar la informacion del formulario de generacion de energia.
				guardarInfoGeneracionEnergia(periodo, idEmpre);
			}
		}
		if($("#idmesinf").val() == 0 && ($("#iddil").val() > 0 && $("#iddil").val() < 12)){
			alert("Debe seleccionar una causa si los meses de operaci\u00f3n fueron inferiores a 12");
			$("#idmesinf").focus();
			error = true;
		}

		if($("#iddil").val() == 0 ){
			alert("Meses de operaci\u00f3n debe ser mayor que cero.");
			error = true;
		}

		if (!error){
			//Guardar el formulario
			document.getElementById("idgrabacara").disabled = true;

			queryString = "numemp="+escape(Empresa);
			for (i=0; i<document.formcara.elements.length; i++){
				if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3"){
					if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked){
						queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
					}
					else if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button"){
						queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
					}
				}
			}
			queryString = queryString + "&opcomex=" + opcomex;
			var url = "eas_caratula_registra.php";
			$.ajax({
				type: "POST",
				url: url,
				data: queryString,
				success: function( data ) {
					var respuesta = JSON.parse( data );
					if( respuesta.error == 0 ){
						document.getElementById("idgrabacara").disabled = false;
						$( "#respuesta" ).html( respuesta.mensaje );
	                    parent.frames['cabeza'].document.getElementById('opm1').style.backgroundColor = '#00008B';
	                    parent.frames['cabeza'].document.getElementById('opm1').style.color = '#FFF';
						if( parseInt(respuesta.cabeza ) == 1 ){
							parent.frames['cabeza'].document.getElementById('indica').style.display = 'line';
	            			parent.frames['cabeza'].document.getElementById('indica').style.visibility = 'visible';
						}
					}else{
						$( "#respuesta" ).html( respuesta.mensaje );
						document.getElementById("idgrabacara").disabled = false;
					}
		      	}
		    });
		}
	}





function grabaCaraCoordinador2020(idEmpre, actividad, periodo)
{

	var resthot = ["5511", "5512", "5513", "5519", "5521", "5522", "5523", "5524", "5529", "5530"];
	var tya = ["6310", "6320", "6331", "6332", "6333", "6339", "6390"];
	var cyc = ["6421", "6422", "6423", "6424", "6425", "6426", "6411", "6412"];
	var finan = ["7010", "7020", "7111", "7112", "7113", "7121", "7122", "7123", "7129", "7130", "7210", "7220", "7230", "7240", "7250", "7290", "7310", "7320", "7411", "7412", "7413", "7414", "7421", "7422", "7430", "7491", "7492", "7493", "7494", "7495", "7499"];
	var comuna = ["6340", "7921", "8050", "8511", "8512", "8513", "8514", "8515", "8519", "9212", "9213", "9214", "9219", "9220", "9301", "9302", "9303", "9309"];
	var Empresa = idEmpre;
	xmlHttpGecara=creaObjCara();
	var queryString = "";

	if (xmlHttpGecara == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;
	for (i=0; i<document.formcara.elements.length; i++)
	{
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar)
		{
			case "idtipodo":
				if (document.formcara.elements[i].checked == true)
				{
					tipoDoc = valorcheck;
				}
				break;
			case "idnitcc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el N\xDAMERO DE DOCUMENTO\n";
				}
				numeroDoc = valorcheck;
				break;
			case "iddv":
				if (tipoDoc == 1)
				{
					resdigi = checkDigi(numeroDoc);
					if (resdigi != valorcheck)
					{
						mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
						errores = "SI";
					}
				}
				break;
			case "idproraz":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la RAZ\xD3N SOCIAL\n";
				}
				break;
			case "idnomcom":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
				}
				break;
			case "iddirecc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N\n";
				}
				break;
			case "idtel":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO\n";
				}
				break;
			case "dirn":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N DE NOTIFICACI\xD3N\n";
				}
				break;
			case "idtelno":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO DE NOTIFICACI\xD3N\n";
				}
				break;

		}
	}
	var sigue = 1;


	if (errores == "SI")
	{
		alert(mensaerr);
		return;
	}
	document.getElementById("idgrabacara").disabled = true;
	var url = "eas_grabacara.php";
	if( parseInt( periodo ) == 2020 )
		url = "eas_caratula_registra.php";

	queryString = "numemp="+escape(Empresa);
	for (i=0; i<document.formcara.elements.length; i++)
	{
		if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3")
		{
			if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked)
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
			else	if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button")
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
		}
	}
	xmlHttpGecara.open("POST", url, true);
	xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpGecara.onreadystatechange=estadoRetCara2;
	xmlHttpGecara.send(queryString);
}


function grabaCaraCoordinador2020Dir(idEmpre, actividad, periodo)
{

	var resthot = ["5511", "5512", "5513", "5519", "5521", "5522", "5523", "5524", "5529", "5530"];
	var tya = ["6310", "6320", "6331", "6332", "6333", "6339", "6390"];
	var cyc = ["6421", "6422", "6423", "6424", "6425", "6426", "6411", "6412"];
	var finan = ["7010", "7020", "7111", "7112", "7113", "7121", "7122", "7123", "7129", "7130", "7210", "7220", "7230", "7240", "7250", "7290", "7310", "7320", "7411", "7412", "7413", "7414", "7421", "7422", "7430", "7491", "7492", "7493", "7494", "7495", "7499"];
	var comuna = ["6340", "7921", "8050", "8511", "8512", "8513", "8514", "8515", "8519", "9212", "9213", "9214", "9219", "9220", "9301", "9302", "9303", "9309"];
	var Empresa = idEmpre;
	xmlHttpGecara=creaObjCara();
	var queryString = "";

	if (xmlHttpGecara == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;
	for (i=0; i<document.formcara.elements.length; i++)
	{
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar)
		{
			case "idtipodo":
				if (document.formcara.elements[i].checked == true)
				{
					tipoDoc = valorcheck;
				}
				break;
			case "idnitcc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el N\xDAMERO DE DOCUMENTO\n";
				}
				numeroDoc = valorcheck;
				break;
			case "iddv":
				if (tipoDoc == 1)
				{
					resdigi = checkDigi(numeroDoc);
					if (resdigi != valorcheck)
					{
						mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
						errores = "SI";
					}
				}
				break;
			case "idproraz":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la RAZ\xD3N SOCIAL\n";
				}
				break;
			case "idnomcom":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
				}
				break;
			case "iddirecc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N\n";
				}
				break;
			case "idtel":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO\n";
				}
				break;
			case "dirn":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N DE NOTIFICACI\xD3N\n";
				}
				break;
			case "idtelno":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO DE NOTIFICACI\xD3N\n";
				}
				break;

		}
	}
	var sigue = 1;


	if (errores == "SI")
	{
		alert(mensaerr);
		return;
	}
	document.getElementById("idgrabacara").disabled = true;
	var url = "eas_grabacara.php";
	if( parseInt( periodo ) == 2020 )
		url = "eas_caratula_registraDir.php";

	queryString = "numemp="+escape(Empresa);
	for (i=0; i<document.formcara.elements.length; i++)
	{
		if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3")
		{
			if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked)
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
			else	if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button")
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
		}
	}
	xmlHttpGecara.open("POST", url, true);
	xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpGecara.onreadystatechange=estadoRetCara2;
	xmlHttpGecara.send(queryString);
}



function grabaCaraCoordinador(idEmpre, actividad )
{

	var resthot = ["5511", "5512", "5513", "5519", "5521", "5522", "5523", "5524", "5529", "5530"];
	var tya = ["6310", "6320", "6331", "6332", "6333", "6339", "6390"];
	var cyc = ["6421", "6422", "6423", "6424", "6425", "6426", "6411", "6412"];
	var finan = ["7010", "7020", "7111", "7112", "7113", "7121", "7122", "7123", "7129", "7130", "7210", "7220", "7230", "7240", "7250", "7290", "7310", "7320", "7411", "7412", "7413", "7414", "7421", "7422", "7430", "7491", "7492", "7493", "7494", "7495", "7499"];
	var comuna = ["6340", "7921", "8050", "8511", "8512", "8513", "8514", "8515", "8519", "9212", "9213", "9214", "9219", "9220", "9301", "9302", "9303", "9309"];
	var Empresa = idEmpre;
	xmlHttpGecara=creaObjCara();
	var queryString = "";

	if (xmlHttpGecara == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;
	for (i=0; i<document.formcara.elements.length; i++)
	{
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar)
		{
			case "idtipodo":
				if (document.formcara.elements[i].checked == true)
				{
					tipoDoc = valorcheck;
				}
				break;
			case "idnitcc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el N\xDAMERO DE DOCUMENTO\n";
				}
				numeroDoc = valorcheck;
				break;
			case "iddv":
				if (tipoDoc == 1)
				{
					resdigi = checkDigi(numeroDoc);
					if (resdigi != valorcheck)
					{
						mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
						errores = "SI";
					}
				}
				break;
			case "idproraz":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la RAZ\xD3N SOCIAL\n";
				}
				break;
			case "idnomcom":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
				}
				break;
			case "iddirecc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N\n";
				}
				break;
			case "idtel":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO\n";
				}
				break;
			case "dirn":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI\xD3N DE NOTIFICACI\xD3N\n";
				}
				break;
			case "idtelno":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL\xC9FONO DE NOTIFICACI\xD3N\n";
				}
				break;

		}
	}
	var sigue = 1;


	if (errores == "SI")
	{
		alert(mensaerr);
		return;
	}
	document.getElementById("idgrabacara").disabled = true;
	var url = "eas_grabacara.php";
	queryString = "numemp="+escape(Empresa);
	for (i=0; i<document.formcara.elements.length; i++)
	{
		if (document.formcara.elements[i].name != "regional" && document.formcara.elements[i].name != "ciiu3")
		{
			if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked)
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
			else	if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button")
			{
				queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
			}
		}
	}
	xmlHttpGecara.open("POST", url, true);
	xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpGecara.onreadystatechange=estadoRetCara2;
	xmlHttpGecara.send(queryString);
}


function estadoRetCara()
{
	if (xmlHttpGecara.readyState==4 || xmlHttpGecara.readyState=="complete")
	{
		if (xmlHttpGecara.responseText == "EACTI")
		{
			alert("ERROR - LA SUMATORIA DE LAS ACTIVIDADES DE LA EMPRESA NO ES IGUAL A 100");
		}
		else
		{
			document.getElementById("respuesta").innerHTML=xmlHttpGecara.responseText;
            parent.frames['cabeza'].document.getElementById('opm1').style.backgroundColor = '#00008B';
            parent.frames['cabeza'].document.getElementById('opm1').style.color = '#FFF';
            //parent.frames['cabeza'].document.getElementById('indica').style.visibility = 'visible';
		}
		document.getElementById("idgrabacara").disabled = false;
	}
	else{
		document.getElementById("respuesta").innerHTML = "";
	}
}

function estadoRetCara2()
{
	if (xmlHttpGecara.readyState==4 || xmlHttpGecara.readyState=="complete")
	{

			document.getElementById("respuestaDatosGenerales").innerHTML=xmlHttpGecara.responseText;
//			parent.frames['cabeza'].document.getElementById('opm1').style.backgroundColor = '#00008B';
//			parent.frames['cabeza'].document.getElementById('opm1').style.color = '#FFF';
//			parent.frames['cabeza'].document.getElementById('indica').style.visibility = 'visible';

		document.getElementById("idgrabacara").disabled = false;
	}
}

function creaObjCara()
{
	var xmlHttpGecara = null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		xmlHttpGecara = new XMLHttpRequest();
	}
	catch (e)
	{
 // Internet Explorer
	try
    {
		xmlHttpGecara = new ActiveXObject("Msxml2.XMLHTTP");
    }
	catch (e)
    {
		xmlHttpGecara = new ActiveXObject("Microsoft.XMLHTTP");
    }
 }
  return xmlHttpGecara;
}

function checkBlanco(nombre, valor)
{
	valor = valor.split(" ").join("");
	var retorno = "OK";
	if (valor.length == 0 || valor == " ")
	{
		retorno = "vacio";
	}
	return retorno;
}

function checkFechas(nombre, valor)
{
	var retorno = "OK";
	if (valor.length == 0)
	{
		retorno = "vacio";
	}
	else
	{
		fechacheck = valor.split("-");
		//if (parseInt(fechacheck[0]) < 1800 || parseInt(fechacheck[0]) > 2010)
		if (parseInt(fechacheck[0]) < 1800 || parseInt(fechacheck[0]) > anio_actual)
		{
			retorno = "AER";
		}
		if (parseInt(fechacheck[1]) < 0 || parseInt(fechacheck[1]) > 12)
		{
			retorno = "AER";
		}
		if (parseInt(fechacheck[2]) < 0 || parseInt(fechacheck[2]) > 31)
		{
			retorno = "AER";
		}
	}
	return retorno;
}

function checkFechah(nombre, valor)
{
	var retorno = "OK";
	if (valor.length == 0)
	{
		retorno = "vacio";
	}
	else
	{
		fechahasta = valor.split("-");
		if (parseInt(fechahasta[0]) > 0)
		{
			if (fechahasta[1] < 1 || fechahasta[1] > 12)
			{
				retorno = "AER";
			}
			if (fechahasta[2] < 1 || fechahasta[2] > 31)
			{
				retorno = "AER";
			}
		}
	}
	return retorno;
}

function checkCapital(nombre, valor)
{
	var retorno = "OK";
	var nalpub = parseInt(document.getElementById("idnalpub").value);
	var nalpr = parseInt(document.getElementById("idnalpr").value);
	var expub = parseInt(document.getElementById("idexpub").value);
	var expr = parseInt(document.getElementById("idexpr").value);
	if (nalpub+nalpr+expub+expr != 100)
	{
		retorno = "ERR";
	}
	return retorno;
}

function checkEstind(nombre, valor)
{
	var retorno = "OK";
	if (valor.length == 0 || parseInt(valor) == 0)
	{
		retorno = "ERR";
	}
	return retorno;
}

 function checkNumCara(id, valor)
{
	idCampo = id;
	var numeros = "0123456789";
	var numerico = true;
	valor = valor.split(" ").join("");
	if (valor.length == 0)
	{
		numerico = false;
		alert("EL CAMPO DEBE SER NUMERICO,  SI NO TIENE INFORMACI?N DIGITE CERO (0)");
		setTimeout("document.getElementById(idCampo).focus();", 10);
	}
	var caja;
	for (i = 0; i<valor.length && numerico == true; i++)
	{
		caja = valor.charAt(i);
		if (numeros.indexOf(caja) == -1)
		{
			numerico = false;
			alert("EL CAMPO DEBE SER NUMERICO,  SI NO TIENE INFORMACI?N DIGITE CERO (0)");
			setTimeout("document.getElementById(idCampo).focus();", 10);
		}
		else
		{
			document.getElementById(idCampo).value = valor;
		}
	}
}

function fechaIni(campo, fechaCheck)
{
	var campoFecha = campo;
	alert(document.getElementById(campoFecha).value);
	var valor = fechaCheck.split(" ").join("");
	if (valor.length == 0)
	{
		alert("DEBE DILIGENCIAR LA FECHA DE CONSTITUCI?N DE LA EMPRESA");
		setTimeout("document.getElementById(campoFecha).focus();", 10);
	}
	else
	{
		chequeo = valor.split("-");
		if (parseInt(chequeo[0]) < 1820 || parseInt(chequeo[0]) > 2008)
		{
			alert("A?O FECHA DE CONSTITUCI?N INVALIDO");
			setTimeout("document.getElementById(campoFecha).focus();", 10);
			return;
		}
		if (parseInt(chequeo[1]) < 1 || parseInt(chequeo[1]) > 12)
		{
			alert("MES FECHA DE CONSTITUCI?N INVALIDO");
			setTimeout("document.getElementById(campoFecha).focus();", 10);
			return;
		}
		if (parseInt(chequeo[2]) < 0 || parseInt(chequeo[2]) > 31)
		{
			alert("DIA FECHA DE CONSTITUCI?N INVALIDO");
			setTimeout("document.getElementById(campoFecha).focus();", 10);
			return;
		}
	}
}

function checkDigi(valor)
{
	var suma = 0;
	var parte = 0;
	var vector2 = [71,  67,  59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
	var vector1 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
	var indice = (15 - valor.length);
	l = 0;

	for (k=indice; indice<15; indice++)
	{
		vector1[indice] = valor.charAt(l);
		l++;
	}
	for (j=0; j<15; j++)
	{
		parte = (parseInt(vector1[j]) * parseInt(vector2[j]));
		suma = suma + parte;
	}
	var resto = suma % 11;
	if (resto == 0 || resto == 1)
	{
		return resto;
	}
	else
	{
		resto = 11 - resto;
		return resto;
	}
}

function limpiaCara()
{
	var ceros = ["ccspun", "ccsprn", "ccspue", "ccspre", "idag", "idnmi", "idnsp", "idncoc", "idnes", "idntp", "idncomu", "idnfs", "idnsc", "idnma", "idnulc"];
	var numero = "NO";
	for (i=0; i<document.formcara.elements.length; i++)
	{
		if(document.formcara.elements[i].type=="radio")
		{
			document.formcara.elements[i].checked = false;
		}
		else	if(document.formcara.elements[i].type=="text")
		{
			if (document.formcara.elements[i].name == "idfcd" || document.formcara.elements[i].name == "idfch")
			{
				document.formcara.elements[i].value = "0000-00-00";
			}
			else
			{
				numero = "NO";
				for (j=0; j<21; j++)
				{
					if (document.formcara.elements[i].name == ceros[j])
					{
						numero = "SI";
						break;
					}
				}
				if (numero == "SI")
				{
					document.formcara.elements[i].value = 0;
				}
				else
				{
					document.formcara.elements[i].value = "";
				}
			}
		}
	}
	document.getElementById("rnit").focus();
	document.getElementById("marcociiu").style.display = "block";
	document.getElementById("idgrabacara").style.display = "none";
	document.getElementById("insecara").style.display = "block";
	document.getElementById("idactiv").style.display = "none";
}

var xmlInsCara;
function insertaCara()
{
	xmlInsCara = creaObjCara();
	var queryString = "";

	if (xmlInsCara == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}
	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;
	for (i=0; i<document.formcara.elements.length; i++)
	{
		var nombrevar = document.formcara.elements[i].name;
		var valorcheck = document.formcara.elements[i].value;
		switch (nombrevar)
		{
			case "idtipodo":
				if (document.formcara.elements[i].checked == true)
				{
					tipoDoc = valorcheck;
				}
				break;
			case "idnitcc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el NUMERO DE DOCUMENTO\n";
				}
				numeroDoc = valorcheck;
				break;
			case "iddv":
				if (tipoDoc == 1)
				{
					resdigi = checkDigi(numeroDoc);
					if (resdigi != valorcheck)
					{
						mensaerr = mensaerr+"NIT O D?GITO DE VERIFICACI?N INV?LIDO\n";
						errores = "SI";
					}
				}
				break;
			case "idproraz":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la RAZ?N SOCIAL\n";
				}
				break;
			case "idnomcom":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el NOMBRE COMERCIAL\n";
				}
				break;
			case "iddirecc":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI?N\n";
				}
				break;
			case "idtel":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL?FONO\n";
				}
				break;
			case "iddino":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la DIRECCI?N DE NOTIFICACI?N\n";
				}
				break;
			case "idtelno":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar el TEL?FONO DE NOTIFICACI?N\n";
				}
				break;
			case "idfcd":
				resulta = checkFechas(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI?N\n";
				}
				if (resulta == "AER")
				{
					errores = "SI";
					mensaerr = mensaerr+"FECHA DE CONSTITUCI?N [DESDE] INVALIDA\n";
				}
				break;
			case "idfch":
				resulta = checkFechah(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar la FECHA DE CONSTITUCI?N [HASTA]\n";
					mensaerr = mensaerr+"Si NO tiene esta fecha entre [0000-00-00]\n";
				}
				if (resulta == "AER")
				{
					errores = "SI";
					mensaerr = mensaerr+"FECHA DE CONSTITUCI?N [HASTA] INVALIDA\n";
				}
				break;
			case "ccspre":
				resulta = checkCapital(nombrevar, valorcheck);
				if (resulta == "ERR")
				{
					errores = "SI";
					mensaerr = mensaerr+"La sumatoria de los componentes del CAPITAL SOCIAL debe ser igual a 100%\n";
				}
				break;
			case "responde":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar PERSONA QUE DILIGENCIA\n";
				}
				break;
			case "teler":
				resulta = checkBlanco(nombrevar, valorcheck);
				if (resulta == "vacio")
				{
					errores = "SI";
					mensaerr = mensaerr+"Debe diligenciar TEL?FONO DE PERSONA QUE DILIGENCIA\n";
				}
				break;
		}
	}
	if (errores == "SI")
	{
		alert(mensaerr);
		return;
	}
	document.getElementById("insecara").disabled = true;
	var url="eas_insecara.php";
	queryString = "numemp="+0;
	for (i=0; i<document.formcara.elements.length; i++)
	{
		if(document.formcara.elements[i].type=="radio" && document.formcara.elements[i].checked)
		{
			queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
		}
		else	if(document.formcara.elements[i].type!="radio" && document.formcara.elements[i].type!="button")
		{
			queryString=queryString+"&"+document.formcara.elements[i].name+"="+escape(document.formcara.elements[i].value);
		}
	}
	xmlInsCara.open("POST", url, true);
	xmlInsCara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlInsCara.onreadystatechange=retInsCara;
	xmlInsCara.send(queryString);
}

function retInsCara()
{
	if (xmlInsCara.readyState==4 || xmlInsCara.readyState=="complete")
	{
		document.getElementById("respuesta").innerHTML=xmlInsCara.responseText;
	}
}

function checkmes(meses)
{
	if  (parseInt(meses) > 12 || parseInt(meses) < 1)
	{
		alert("Meses de operacion deben estar entre un rango de 1 a 12");
		return false;
4	}

	if  (parseInt(meses) > 0 && parseInt(meses) < 12)
	{
		document.getElementById("idlmes").style.display="block";
		document.getElementById("idmesinf").style.display="block";
	}else if(parseInt(meses) == 12){

		document.getElementById("idlmes").style.display="none";
		document.getElementById("idmesinf").style.display="none";
		document.getElementById("idmesinf").value="0";

		document.getElementById("divmes").style.display="none";
		document.getElementById("idmesmen").style.display="none";
		document.getElementById("idmesmen").value="";

	}
}
function AbreAdic()
{
        document.body.style.overflow="hidden";
        //document.getElementById("div_totalPantalla").style.visibility="visible";
	document.getElementById("empadic").style.display = "block";
	document.getElementById("nord").focus();
	//document.getElementById("idvalor").value = 0;
}

function cierraAdic()
{
	document.getElementById("empadic").style.display = "none";
        document.getElementById("div_totalPantalla").style.visibility="";
        document.body.style.overflow="auto";
}

var xmlHttpGremp;
function grabarEmp(idnord, idenit, idcce, idcec, idnitc, idendv, idciiu, idnomcom, idsede)
{
	xmlHttpGremp=creaObjCara();
	var queryString = "";
	var errores = "NO";
	var resulta = "";
	var mensaerr = "";
	var tipoDoc;
	var numeroDoc;
	var Empresa = idnord;
	var nombrevar = "Empresa"
	var valorcheck = document.getElementById(Empresa).value;
	resulta = checkBlanco(Empresa, valorcheck);
	if (resulta == "vacio")
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE DIGITAR EL NUMERO DE ORDEN DE LA EMPRESA\n";
	}
	numeroDoc = valorcheck;

	var venit = document.getElementById(idenit).value;
	var vcce = document.getElementById(idcce).value;
	var vcec = document.getElementById(idcec).value;
	var vnitc = document.getElementById(idnitc).value;
	var vendv = document.getElementById(idendv).value;
	var vciiu = document.getElementById(idciiu).value;
	var vnomcom = document.getElementById(idnomcom).value;
	var vsede = document.getElementById(idsede).value;
	if (document.getElementById(idenit).checked == false && document.getElementById(idcce).checked == false && document.getElementById(idcec).checked == false)
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE DIGITAR EL TIPO DE DOCUMENTO DE IDENTIFICACION DE LA EMPRESA\n";
	}
	if (document.getElementById(idenit).checked == true)
	{
		tipoDoc = "1";
	}
	if (document.getElementById(idcce).checked == true)
	{
		tipoDoc = "2";
	}
	if (document.getElementById(idcec).checked == true)
	{
		tipoDoc = "3";
	}
	if (vnitc == 0)
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE DIGITAR EL NUMERO DE IDENTIFICACION DE LA EMPRESA\n";
	}

	if (document.getElementById(idenit).checked == true)
	{
		resdigi = checkDigi(vnitc);
		if (resdigi != vendv)
		{
			mensaerr = mensaerr+"NIT O D\xCDGITO DE VERIFICACI\xD3N INV\xC1LIDO\n";
			errores = "SI";
		}
	}
	//alert(vperio);
	if (vsede == "0")
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE SELECCIONAR LA SEDE DE LA EMPRESA\n";
	}
	if (vciiu == "0")
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE SELECCIONAR ACTIVIDAD ECONOMICA DE LA EMPRESA\n";
	}
	if (vnomcom == "")
	{
		errores = "SI";
		mensaerr = mensaerr+"DEBE DIGITAR EL NOMBRE COMERCIAL DE LA EMPRESA\n";
	}

	if (errores == "SI")
	{
		alert(mensaerr);
		return;
	}

	if (xmlHttpGremp == null)
	{
		alert ("El explorador no soporta solicitudes HTTP");
		return;
	}

	var url="eas_grabaemp.php";
	queryString = "numemp="+escape(numeroDoc)+"&tipod="+escape(tipoDoc)+"&nitcc="+escape(vnitc)+"&dv="+escape(vendv)+"&activ="+escape(vciiu)+"&nomcom="+escape(vnomcom)+"&sede="+escape(vsede);
	//alert(queryString);
	xmlHttpGremp.open("POST", url, true);
	xmlHttpGremp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpGremp.setRequestHeader("Content-length", queryString.length);
	xmlHttpGremp.onreadystatechange=estadoRetGemp;
	xmlHttpGremp.send(queryString);
}

function estadoRetGemp()
{
	if (xmlHttpGremp.readyState==4 || xmlHttpGremp.readyState=="complete")
	{
		document.getElementById("empadic").innerHTML = xmlHttpGremp.responseText;
	}
}


/**
 * Funcion para guardar en la base de datos la informacion del formulario de generacion de energia de la caratula
 * Utiliza JQuery.min.js para hacer ajax y guardar los registros de la tabla de generacion de energía
 * @author: dmdiazf
 * @since:  01/03/2017
 **/
function guardarInfoGeneracionEnergia(periodo, idnoremp){

	var url = "eas_grabaenergia.php";
	if( parseInt( periodo ) == 2020 )
		url = "../eas_grabaenergia.php";



	$.ajax({
		type: "POST",
		url: url,
		data: {
		  'periodo': periodo,
		  'idnoremp': idnoremp,
		  'nplante': (parseInt($("#NPLANTE").val()) > 0)?$("#NPLANTE").val():0,
		  'ckwpe': (parseInt($("#CKWPE").val()) > 0)?$("#CKWPE").val():0,
		  'ttplane': (parseInt($("#TTPLANE").val()) > 0)?$("#TTPLANE").val():0,
		  'cplanteg': (parseInt($("#CPLANTEG").val()) > 0)?$("#CPLANTEG").val():0,
		  'cplanted': (parseInt($("#CPLANTED").val()) > 0)?$("#CPLANTED").val():0,
		  'cplanteo': (parseInt($("#CPLANTEO").val()) > 0)?$("#CPLANTEO").val():0,
		  'nplantr': (parseInt($("#NPLANTR").val()) > 0)?$("#NPLANTR").val():0,
		  'ckwpr': (parseInt($("#CKWPR").val()) > 0)?$("#CKWPR").val():0,
		  'ttplanr': (parseInt($("#TTPLANR").val()) > 0)?$("#TTPLANR").val():0,
		  'cplantrg': (parseInt($("#CPLANTRG").val()) > 0)?$("#CPLANTRG").val():0,
		  'cplantrd': (parseInt($("#CPLANTRD").val()) > 0)?$("#CPLANTRD").val():0,
		  'cplantro': (parseInt($("#CPLANTRO").val()) > 0)?$("#CPLANTRO").val():0,
		  'nplants': (parseInt($("#NPLANTS").val()) > 0)?$("#NPLANTS").val():0,
		  'ckwps': (parseInt($("#CKWPS").val()) > 0)?$("#CKWPS").val():0,
		  'ttplans': (parseInt($("#TTPLANS").val()) > 0)?$("#TTPLANS").val():0,
		  'nplanteo': (parseInt($("#NPLANTEO").val()) > 0)?$("#NPLANTEO").val():0,
		  'ckwpeo': (parseInt($("#CKWPEO").val()) > 0)?$("#CKWPEO").val():0,
		  'ttplaneo': (parseInt($("#TTPLANEO").val()) > 0)?$("#TTPLANEO").val():0,
		  'nplanto': (parseInt($("#NPLANTO").val()) > 0)?$("#NPLANTO").val():0,
		  'ckwpo': (parseInt($("#CKWPO").val()) > 0)?$("#CKWPO").val():0,
		  'ttplano': (parseInt($("#TTPLANO").val()) > 0)?$("#TTPLANO").val():0,
		  'emcual': $("#EMCUAL").val(),
		  'rescual': $("#RESCUAL").val(),
		  'otrcual': $("#OTRCUAL").val()
		},
		dataType: "html",
		contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		cache: false,
		success: function(data){
			//alert(data);
			return true;
		},
		error: function (request, status, error) {
			alert(request.responseText);
		}
	});
}






