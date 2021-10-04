<?php

	session_start();
	include '../../servicio/eas_conecta.php';	
	require ("gen_energia2018.php"); //Funciones para el formulario de energia periodo 2018
	$idusuario = $_SESSION['idusu'];
	$region = $_SESSION['region'];
	$tipousu = $_SESSION['tipou'];
	$periodo = $_SESSION['anoproc'];  //Contiene el periodo almacenado en la session.
	
	
	if (session_id() == "") {
		session_start();
	}
	
	$causas = array(0=>"Seleccione una Causa", 1=>"Liquidada", 2=>"Por Huelga", 3=>"Por Traslado", 4=>"Por Ampliaci&oacute;n", 5=>"Por Fusi&oacute;n", 6=>"Otra");
	
	if ($_SESSION['tipou'] == "FU") {
		$consulta="SELECT * FROM eas_directorio WHERE idnoremp=" . $_SESSION['numero'] . " AND periodo = " . $periodo . " LIMIT 1";
	}
	else {
		if (isset($_POST['cadenab'])) {
			if ($region == 99) {
				$consulta = "SELECT * FROM eas_directorio WHERE idnoremp=" . trim($_POST['cadenab']) . " AND periodo = " . $periodo . " LIMIT 1";
			}
			else {
				$consulta = "SELECT * FROM eas_directorio WHERE idnoremp=" . trim($_POST['cadenab']) . " AND periodo = " . $periodo .  " AND csede=" . $region . " LIMIT 1";
			}
		}
		else {
			if (isset($_GET['idemp'])) {
				if ($region == 99) {
					$consulta = "SELECT * FROM eas_directorio WHERE idnoremp=" . $_GET['idemp'] . " AND periodo = " . $periodo . " LIMIT 1";
					$leectl = "SELECT * FROM eas_control WHERE periodo = " . $periodo .  " AND idnoremp = " . $_GET['idemp'];
				}
				else {
					$consulta = "SELECT * FROM eas_directorio WHERE idnoremp=" . $_GET['idemp'] . " AND periodo = " . $periodo . " AND csede =" . $region . " LIMIT 1";
					$leectl = "SELECT * FROM eas_control WHERE periodo = " . $periodo .  " AND idnoremp = " . $_GET['idemp'] . " AND codsede =" . $region;
				}
			}
			else {
				if ($region == 99) {
					$consulta = "SELECT * FROM eas_directorio  WHERE periodo = " . $periodo . " LIMIT 1";
				}
				else {
					$consulta = "SELECT * FROM eas_directorio WHERE csede=" . $region . " AND periodo = " . $periodo . " LIMIT 1";
				}
			}
		}
	}	
	
	
	if (isset($_SESSION['debug-dane'])) {
		echo "<pre>";
		print_r($consulta);
		echo "</pre>";
	}
	
	
	$resultado = mysqli_query($con, $consulta);
	if (mysqli_num_rows($resultado) == 0) {
		$existe = "NO";
	}
	else {
		$row = mysqli_fetch_array($resultado);
		$existe = "SI";
		$actividad = '"' . $row['idact'] . '"';
		$actciiu4 = $row["ciiu4"];
		
		
		$car_ciiu = ($periodo < 2013)?$actividad:$actciiu4;
		
		
		$leectl = "SELECT * FROM eas_control WHERE periodo = " . $periodo .  " AND idnoremp = " . $row['idnoremp'];
		$resctl = mysqli_query($con, $leectl);
		$lineactl = mysqli_fetch_array($resctl);
	}
	
	
	if ($periodo<2013){
		//Obtener nombres de actividad de la empresa para la CIIU Versi�n 3.
		$desciiu = "SELECT codigo, descrip FROM eas_ciiu3com WHERE codigo = " . $row['idact'];
	}
	else{
		//Obtener nombres de actividad de la empresa para la CIIU Version 4.
		$actciiu4 = (isset($actciiu4))?$actciiu4:$row["idact"];		
		$desciiu = "SELECT codigo, descrip FROM eas_ciiu4com WHERE codigo = " . $actciiu4;
	}
	
	//var_dump($desciiu);
	
	
	$resciiu = mysqli_query($con, $desciiu);
	if (mysqli_num_rows($resciiu) == 0) {
		$lactividad = "ACTIVIDAD NO DEFINIDA";
	}
	else {
		$lineacti = mysqli_fetch_array($resciiu);
		$lactividad = $lineacti['codigo'] . " - " . $lineacti['descrip'];
	}
	
	
	/**
	 * Funci�n para saber si una fuente es prioritaria o no lo es
	 * @author Daniel M. D�az
	 * @since  2015-04-28
	 **/
	function prioritaria($con, $idnoremp){
		$prioritaria = false;
		$encontrada = 0;
		$sql = "SELECT COUNT(*) AS found
		        FROM eas_prioritarias
		        WHERE idnoremp = $idnoremp";
		$res = mysqli_query($con, $sql);
		while ($row = mysqli_fetch_array($res)){
			$encontrada = $row["found"];
		}
		mysqli_free_result($res);
		if ($encontrada > 0){
			$prioritaria = true;
		}
		return $prioritaria;
	}
	
	
	
	/**
	 * Funcion para bloquear la caratula dependiendo del tipo de usuario que accede
	 * @author Daniel M. Díaz
	 * @since  2016/05/05
	 */
	function bloquearCaratula($idusuario){
		$bloqueo = false;
		$siglasFU = substr($idusuario, 0, 1); 
		$siglas = substr($idusuario, 0, 4);
		if ($siglasFU!='F'){
			if (($siglas!='CO99')&&($siglas!='CR99')){
				$bloqueo = true;
			}
			if ($bloqueo){
				echo "disabled='disabled'";
			}	
		}		
	}
	
	
	
	
	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Encuesta Anual Manufacturera - Formulario Electr&oacute;nico</title>
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Last-Modified" content="0">
		<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<link rel = stylesheet href = "../../css/formelec.css" type="text/css">
		<link rel="stylesheet" href="../../js/jqueryui/jquery-ui.min.css" type="text/css">
		<script type="text/javascript">
			var fecha = new Date();
			var anio_actual = fecha.getFullYear();				
		</script>
		<script type="text/javascript" src="../js/buscara.js"></script>
		<script type="text/javascript" src="../js/eas_grabarcara.js"></script>
		<script type="text/javascript" src="../js/cargamuni.js"></script>
		<script type="text/javascript" src="../js/observa.js"></script>
		<script type="text/javascript" src="../js/grabaracti.js"></script>
		<script type="text/javascript" src="../js/borraracti.js"></script>
		<script type="text/javascript" src="../js/grbadist.js"></script>
		<script type="text/javascript" src="../js/eas_muestraotros.js"></script>
		<script type="text/javascript" src="../../js/jquery.min.js"></script>
		<script type="text/javascript" src="../js/cambio_actividad.js"></script>
		<script type="text/javascript" src="../../js/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/jqueryui/jquery-ui.min.js"></script>
		<script>
			
			//Bloquear el ingreso de caracteres de texto sobre un campo que solo permita numeros
			$.fn.numerico = function() {
				return this.keypress(function(event){
					if ((event.which == 8)||(event.which == 0)) return true;
					if ((event.which >=48)&&(event.which <=57)) 
						return true;
					else
						return false;    		
				});    
			};
			
			$(function(){	
			
					var xmlHttpGecara;
					
					xmlHttpGecara=creaObjCara();
					if (xmlHttpGecara == null){
						alert ("El explorador no soporta solicitudes HTTP");
						return;
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
					
					
				    function guardaCaracterizacion() {
						
					  
						if($("#linea_negocio").val().length > 300 || $("#linea_negocio").val().length < 1){
							alert('Falta diligenciar la linea de negocio de su empresa.');	
						}else{
							
							var linea = $("#linea_negocio").val();
							
							var url="eas_grabalineanegocio.php";
							queryString = "numemp="+$("#idenempresa").val()+"&periodo="+$("#periodo_linea").val()+"&linea="+$("#linea_negocio").val();
							
							xmlHttpGecara.open("POST", url, true);
							xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							xmlHttpGecara.onreadystatechange=estadoRetCara;
							xmlHttpGecara.send(queryString);
							alert('Informaci\u00F3n guardada con exito');							
							$( "#dialog_linea" ).dialog( "close" );	
						}
					}
					
					function modificaCaracterizacion() {
					  
						if($("#linea_negocio").val().length > 300 || $("#linea_negocio").val().length < 1){
							alert('Falta diligenciar la linea de negocio de su empresa.');	
						}else{
							
							var linea = $("#linea_negocio").val();
							
							var url="eas_grabalineanegocio.php";
							queryString = "numemp="+$("#idenempresa").val()+"&periodo="+$("#periodo_linea").val()+"&linea="+$("#linea_negocio").val();
							
							xmlHttpGecara.open("POST", url, true);
							xmlHttpGecara.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							xmlHttpGecara.onreadystatechange=estadoRetCara;
							xmlHttpGecara.send(queryString);
							alert('Informaci\u00F3n guardada con exito');							
							$( "#dialog_linea" ).dialog( "close" );	
						}
					}
						
				$( "#dialog_linea" ).dialog({
				  autoOpen: true,
				  modal: true,
				  height: "auto",
				  width: 500,
				  buttons: {
					"Aceptar": guardaCaracterizacion,
					"Modificar": modificaCaracterizacion	
				  }
				});
			 
				/*$( "#opener1" ).on( "click", function() {
				  $( "#dialog_linea" ).dialog( "open" );
				});*/
			
			
				if ($("#NPLANTE").length > 0){ 
					$("#NPLANTE").numerico(); 
					$("#NPLANTE").bind("blur",function(){
						if (parseInt($(this).val()) <= 0){
							$("#CKWPE").val("0");
							$("#CKWPE").attr("disabled",true);
							
							$("#TTPLANE").val("0");
							$("#TTPLANE").attr("disabled",true);
							$("#radEme1, #radEme2, #radEme3").attr("disabled",true);
							$("#CPLANTEG").val("0");
							$("#CPLANTEG").attr("disabled",true);
							$("#CPLANTED").val("0");
							$("#CPLANTED").attr("disabled",true);
							$("#CPLANTEO").val("0");
							$("#CPLANTEO").attr("disabled",true);
							$("#EMCUAL").val("");
							$("#EMCUAL").attr("disabled",true);
						}
						else{
							$("#CKWPE").val("");
							$("#CKWPE").attr("disabled",false);
							$("#TTPLANE").val("");
							$("#TTPLANE").attr("disabled",false);
							$("#radEme1, #radEme2, #radEme3").attr("disabled",false);
							$("#CPLANTEG").val("");
							$("#CPLANTEG").attr("disabled",false);
							$("#CPLANTED").val("");
							$("#CPLANTED").attr("disabled",false);
							$("#CPLANTEO").val("");
							$("#CPLANTEO").attr("disabled",false);
							$("#EMCUAL").val("");
							$("#EMCUAL").attr("disabled",false);
						}
					});
				} 		
				
				if ($("#CKWPE").length > 0){ $("#CKWPE").numerico(); } 
				if ($("#TTPLANE").length > 0){ $("#TTPLANE").numerico(); }
				if ($("#CPLANTEG").length > 0){ $("#CPLANTEG").numerico(); }
			    if ($("#CPLANTED").length > 0){ $("#CPLANTED").numerico(); }
			    if ($("#CPLANTEO").length > 0){ $("#CPLANTEO").numerico(); }
			    if ($("#NPLANTR").length > 0){ 
					$("#NPLANTR").numerico(); 
					$("#NPLANTR").bind("blur",function(){
						if (parseInt($(this).val()) <= 0){
							$("#CKWPR").val("0");
							$("#CKWPR").attr("disabled",true);
							$("#TTPLANR").val("0");
							$("#TTPLANR").attr("disabled",true);
							$("#radResp1, #radResp2, #radResp3").attr("disabled",true);
							$("#CPLANTRG").val("0");
							$("#CPLANTRG").attr("disabled",true);
							$("#CPLANTRD").val("0");
							$("#CPLANTRD").attr("disabled",true);
							$("#CPLANTRO").val("0");
							$("#CPLANTRO").attr("disabled",true);
							$("#RESCUAL").val("");
							$("#RESCUAL").attr("disabled",true);							
						}
						else{
							$("#CKWPR").val("");
							$("#CKWPR").attr("disabled",false);
							$("#TTPLANR").val("");
							$("#TTPLANR").attr("disabled",false);
							$("#radResp1, #radResp2, #radResp3").attr("disabled",false);
							$("#CPLANTRG").val("");
							$("#CPLANTRG").attr("disabled",false);
							$("#CPLANTRD").val("");
							$("#CPLANTRD").attr("disabled",false);
							$("#CPLANTRO").val("");
							$("#CPLANTRO").attr("disabled",false);
							$("#RESCUAL").val("");
							$("#RESCUAL").attr("disabled",false);
						}
					});
				}
				
			    if ($("#CKWPR").length > 0){ $("#CKWPR").numerico(); }
				if ($("#TTPLANR").length > 0){ $("#TTPLANR").numerico(); }
			    if ($("#CPLANTRG").length > 0){ $("#CPLANTRG").numerico(); }
			    if ($("#CPLANTRD").length > 0){ $("#CPLANTRD").numerico(); }
			    if ($("#CPLANTRO").length > 0){ $("#CPLANTRO").numerico(); }
				if ($("#NPLANTS").length > 0){ 
					$("#NPLANTS").numerico(); 
					$("#NPLANTS").bind("blur",function(){
						if (parseInt($(this).val()) <= 0){
							$("#CKWPS").val("0");
							$("#CKWPS").attr("disabled",true);
							$("#TTPLANS").val("0");
							$("#TTPLANS").attr("disabled",true);
						}
						else{
							$("#CKWPS").val("");
							$("#CKWPS").attr("disabled",false);
							$("#TTPLANS").val("");
							$("#TTPLANS").attr("disabled",false);
						}
					});				
				}
				
			    if ($("#CKWPS").length > 0){ $("#CKWPS").numerico(); }
				if ($("#TTPLANS").length > 0){ $("#TTPLANS").numerico(); }
			    if ($("#NPLANTEO").length > 0){ 
					$("#NPLANTEO").numerico(); 
					$("#NPLANTEO").bind("blur",function(){
						if (parseInt($(this).val()) <= 0){
						    $("#CKWPEO").val("0");
							$("#CKWPEO").attr("disabled",true);
							$("#TTPLANEO").val("0");
							$("#TTPLANEO").attr("disabled",true);
						}
						else{
							$("#CKWPEO").val("");
							$("#CKWPEO").attr("disabled",false);
							$("#TTPLANEO").val("");
							$("#TTPLANEO").attr("disabled",false);
						}
					});
				}
				
				if ($("#CKWPEO").length > 0){ $("#CKWPEO").numerico(); }
			    if ($("#TTPLANEO").length > 0){ $("#TTPLANEO").numerico(); }
				if ($("#NPLANTO").length > 0){ 
					$("#NPLANTO").numerico(); 
					$("#NPLANTO").bind("blur",function(){
						if (parseInt($(this).val()) <= 0){
							$("#CKWPO").val("0");
							$("#CKWPO").attr("disabled",true);
							$("#TTPLANO").val("0");
							$("#TTPLANO").attr("disabled",true);
							$("#OTRCUAL").val("");
							$("#OTRCUAL").attr("disabled",true);
						}
						else{
							$("#CKWPO").val("");
							$("#CKWPO").attr("disabled",false);
							$("#TTPLANO").val("");
							$("#TTPLANO").attr("disabled",false);
							$("#OTRCUAL").val("");
							$("#OTRCUAL").attr("disabled",false);
						}
					});
				}
				
				if ($("#CKWPO").length > 0){ $("#CKWPO").numerico(); }
				if ($("#TTPLANO").length > 0){ $("#TTPLANO").numerico(); }
								
				$("#radEme1, #radEme2, #radEme3").bind("click",function(){				
					var numero = parseInt($("#NPLANTE").val());
					var valor = parseInt($(this).val());
					switch(valor){
						case 1: if (numero==1){
									$("#radEme2").attr("checked",false);
									$("#radEme3").attr("checked",false);
									$("#CPLANTEG").val("").attr("disabled",false);
									$("#CPLANTED").val("").attr("disabled",true);
									$("#CPLANTEO").val("").attr("disabled",true);
									$("#EMCUAL").val("").attr("disabled",true);								
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTEG").attr("disabled",false);
									}
									else{
										$("#CPLANTEG").attr("disabled",true);
									}									
								}								
								break;
						case 2: if (numero==1){
									$("#radEme1").attr("checked",false);
									$("#radEme3").attr("checked",false);
									$("#CPLANTEG").val("").attr("disabled",true);
									$("#CPLANTED").val("").attr("disabled",false);
									$("#CPLANTEO").val("").attr("disabled",true);
									$("#EMCUAL").val("").attr("disabled",true);								
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTED").attr("disabled",false);
									}
									else{
										$("#CPLANTED").attr("disabled",true);
									}
								}								
								break;
						case 3: if (numero==1){
									$("#radEme1").attr("checked",false);
									$("#radEme2").attr("checked",false);
									$("#CPLANTEG").val("").attr("disabled",true);
									$("#CPLANTED").val("").attr("disabled",true);
									$("#CPLANTEO").val("").attr("disabled",false);
									$("#EMCUAL").val("").attr("disabled",false);
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTEO").attr("disabled",false);
										$("#EMCUAL").attr("disabled",false);
									}
									else{									
										$("#CPLANTEO").attr("disabled",true);
										$("#EMCUAL").attr("disabled",true);
									}
								}			
								break;
					}					
				});
				
				
				$("#radResp1, #radResp2, #radResp3").bind("click",function(){
					var numero = parseInt($("#NPLANTR").val());
					var valor = parseInt($(this).val());
					switch(valor){
						case 1: if (numero==1){
									$("#radResp2").attr("checked",false);
									$("#radResp3").attr("checked",false);
									$("#CPLANTRG").val("").attr("disabled",false);
									$("#CPLANTRD").val("").attr("disabled",true);
									$("#CPLANTRO").val("").attr("disabled",true);
									$("#RESCUAL").val("").attr("disabled",true);								
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTRG").attr("disabled",false);	
									}
									else{
										$("#CPLANTRG").attr("disabled",true);
									}
								}		
								break;
						case 2: if (numero==1){
									$("#radResp1").attr("checked",false);
									$("#radResp3").attr("checked",false);
									$("#CPLANTRG").val("").attr("disabled",true);
									$("#CPLANTRD").val("").attr("disabled",false);
									$("#CPLANTRO").val("").attr("disabled",true);
									$("#RESCUAL").val("").attr("disabled",true);								
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTRD").attr("disabled",false);
									}
									else{
										$("#CPLANTRD").attr("disabled",true);
									}
								}								
								break;
						case 3: if (numero==1){
									$("#radResp1").attr("checked",false);
									$("#radResp2").attr("checked",false);
									$("#CPLANTRG").val("").attr("disabled",true);
									$("#CPLANTRD").val("").attr("disabled",true);
									$("#CPLANTRO").val("").attr("disabled",false);
									$("#RESCUAL").val("").attr("disabled",false);
								}
								else{
									if ($(this).is(":checked")){
										$("#CPLANTRO").attr("disabled",false);
										$("#RESCUAL").attr("disabled",false);
									}
									else{
										$("#CPLANTRO").attr("disabled",true);
										$("#RESCUAL").attr("disabled",true);
									}									
								}			
								break;
					}
					
				});
				
				
				
			});


			$( document ).tooltip();
		</script>
		
		<style>
          label {
            display: inline-block;
            width: 5em;
          }
          .ui-widget-content{
            font-size: 12px;
          }
          </style>
	</head>
	<body>
        <?php	if ($_SESSION['region'] == 99 OR $tipousu == "CO") { ?>
					<form name="frmbusca" style="position: relative; left: 7.5%; top: 0.1%; margin-bottom: 0px; width: 85%" method="POST" onSubmit="return busCaratula();">
					<fieldset class="marcos">
					<legend>Buscar por:</legend>
					<input type="radio" name="buscar" value="nu" checked /><span class="labela">No Orden</span>
					<input type="radio" name="buscar" value="no" /><span class="labela">Nombre</span>
					<input type="text" id="txtb" name="cadenab" style="width: 41.6%; font-family: verdana; font-size: 9px" />
					<input type="submit" id="btnb" name="envbus" value="Buscar" style="font-family: verdana; font-size: 9px" />
					<?php	//OR $region == 2 OR $region == 4 OR $region == 6  AND ($_SESSION['numero'] == 0)
                           	if (($region == 99 ) AND ($tipousu == "CO")) {
								//echo "<a style='position: absolute; right: 4px' href='#' onClick='AbreAdic();document.getElementById(\"div_totalPantalla\").style.visibility=\"visible\";'>Adicionar Empresa</a>";
								echo "<a style='position: absolute; right: 4px' href='#' onClick='AbreAdic();'>Adicionar Empresa</a>";
								echo "<br>";
							/*}
							if ($idusuario == "CO99001" OR $idusuario == "CO99002") {*/
								echo "<a style='float: right; font-family: arial; font-size: 10px' href='eas_trasede.php?idemp=" . $row['idnoremp'] . "' target='contenido'>Traslado de Sede</a><br />";
								echo "<a style='float: right; font-family: arial; font-size: 10px' href='#' onClick='devolucion(" . $row['idnoremp'] . ", " . $periodo . ");'>Devolver</a>";
							}
							if ($region == 99 AND $tipousu == "CR") {
								echo "<a href='eas_listacara.php' target='contenido' style='position: absolute; right: 2px'>Asignados</a>";
							}
					?>
					</fieldset>
					</form>
		<?php  }
			   if ($existe == "SI") {
		?>	   	<div style="float: right">
					<a href="manual/ayudaCaratula.html" target="_blank"><img style="border: 0px" src="../../images/Help-browser-small.png" /></a>
				</div>

				<?php

				if (($region == 99 AND $tipousu == "CR") || $tipousu == "FU") {
					?>

					<div id="dialog_linea">
						<p class="validateTips">Describa brevemente la linea de negocio de su empresa por la cual percibe su mayor fuente de ingresos.</p>
					    <form>
						<fieldset>
						  <textarea name="linea_negocio" id="linea_negocio" class="text ui-widget-content ui-corner-all" cols="40" rows="3"><?php echo html_entity_decode($row['lineanego']);?></textarea>
						  <input type="hidden" name="idenempresa" id="idenempresa" value="<?php echo $row['idnoremp']?>">
						  <input type="hidden" name="actividad_linea" id="actividad_linea" value="<?php echo $actividad?>">
						  <input type="hidden" name="periodo_linea" id="periodo_linea" value="<?php echo $periodo?>">
						</fieldset>
					  </form>
					</div>

					<?php			
				}

				?>
				
				
				<div style="position: relative; left: 7.5%; width: 85%; font-family: arial; font-size: 11px; margin-bottom: 5px; color: #990000; padding: 3px">
				<b>Modulo I</b> -  <b>Car&aacute;tula &Uacute;nica</b>, Estructura de la Empresa
				<?php  //DANIEL M. DIAZ F.
					   //Abril 05 de 2013
					   //Se Elimina el detalle de la caratula. Se elimina el Bot�n "Ir a Estructura de la empresa", para los periodos superiores al 2012
					   if ($periodo <= 2011){ 
					   		echo "<span style='position: absolute; right: 2px; font-family: arial; font-size: 10px'><b>Ir a:</b>";
							echo "<a class='meresu' href='eas_anexdir.php?idemp=" . $row['idnoremp']  . "&nombremp=" . $row['idnomcom'] . "&periodo=" . $periodo . "'>Estructura de la Empresa</a>";
							echo "</span><br />";
					   } 		
					   
					   if ($tipousu == 'CO' AND $region == 99) {
							echo "<span style='font-family: arial; font-size: 10px;'><a href='#' onClick='mActi();'>Cambiar</a></span>";
					   }
					   
					   
					   if ($periodo <= 2012){
					   		echo "&nbsp;&nbsp;&nbsp;<span style='width: 80%; left: 10%; text-align: center'><b>ACTIVIDAD:</b> " . $lactividad . "</span>";
							echo "<span id='nacti' style='display: none'><b>NUEVA ACTIVIDAD:</b><input id='vnacti' type='text' class='textfecha' value='0' />";
							echo "<a href='#' onClick='cambiActi(" . $periodo . ", " . $row['idnoremp'] . ");'><img src='../../images/45.png' border='0' /></a>";
							echo "</span>";	
					   }
					   else if ($periodo > 2012){
							
							/** OBTENER LOS VALORES DE CIIU3 Y CIIU4 **/
							$actciiu = array();
							$sqlactciiu = "SELECT idact, ciiu4 FROM eas_directorio WHERE periodo = $periodo AND idnoremp = ". $row["idnoremp"];
							$resciiu = mysqli_query($con, $sqlactciiu, );
							if (mysqli_num_rows($resciiu)>0){
								$actciiu = mysqli_fetch_array($resciiu);
							}
							else{ 
								$actciiu["idact"] = 0; 
                                $actciiu["ciiu4"] = 0;                                 
							}
							
							echo "<span style='width: 80%; left: 10%; text-align: center'>";
							echo "<b>ACTIVIDAD:</b>" . $lactividad . "</span>";
							echo "<span id='nacti' style='display: none'>";
							echo "<p>";
							echo "<b>NUEVA ACTIVIDAD CIIU3:&nbsp;&nbsp;</b><input id='vnacti3' type='text' value='".$actciiu["idact"]."' size='6' />&nbsp;&nbsp;<a href='#' onClick='javascript:cambioActividad(" . $periodo . ", " . $row['idnoremp'] . ");'><img src='../../images/45.png' border='0' /></a><br/>";
							echo "<b>NUEVA ACTIVIDAD CIIU4:&nbsp;&nbsp;</b><input id='vnacti4' type='text' value='".$actciiu["ciiu4"]."' size='6' />&nbsp;&nbsp;<a href='#' onClick='javascript:cambioActividad(" . $periodo . ", " . $row['idnoremp'] . ");'><img src='../../images/45.png' border='0' /></a><br/><br/>";
							echo "<b>OBSERVACI&Oacute;N PARA EL CABMIO DE ACTIVIDAD:&nbsp;&nbsp;<br/></b><textarea maxlength='3000' style='width: 70%' name='obsacti' id='obsacti'  cols='100' rows='5' ></textarea>";
							echo "</p>";
							echo "</span>";
					   }
					   
					   if (($tipousu == "CO" OR $tipousu == "CR" OR $tipousu == "TE")) {
				       		if (!isset($_GET['nuevo'])) {
				            	echo "<br><a style='float: right; font-family: arial; font-size: 10px' href='eas_modnove.php?idemp=" . $row['idnoremp'] . "&nomest=" . $row['idnomcom'] . "&region=" . $_SESSION['region'] . "&periodo=" . $periodo . "' target='contenido'>M&oacute;dulo Novedades</a>";
				                echo "<br>";
				            }
			           }
				?>
				</div>
				 
				<form name="formcara" id="idcara" style="position: relative; left: 7.5%; top: 0.1%; margin-bottom: 0px; width: 85%">
				<fieldset class="marcos">
				<legend class="leyenda">Identificaci&oacute;n <span style="color: #E41B17; font-weight: lighter">[<?php echo $row['idnoremp']?>]</span><?php echo (prioritaria($con, $row["idnoremp"]))?"&nbsp;&nbsp;/&nbsp;<span style=\"color: #E41B17; font-weight: lighter\"><b>PRIORITARIA</b></span>":""; ?></legend>
					<?php
					if($row['idtipodo']==1) {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rnit' name='idtipodo' value = '1' checked disabled='disabled'/><span class='labela'>Nit</span>";
						}
						else{
							echo "<input type='radio' id='rnit' name='idtipodo' value = '1' checked /><span class='labela'>Nit</span>";
						}
					}
					else {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rnit' name='idtipodo' value = '1' disabled='disabled' /><span class='labela'>Nit</span>";
						}
						else{
							echo "<input type='radio' id='rnit' name='idtipodo' value = '1' /><span class='labela'>Nit</span>";
						}
					}
					if ($row['idtipodo']==2) {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rcc' name='idtipodo' value = '2' checked disabled='disabled'/><span class='labela'>C.C.</span>";
						}
						else{
							echo "<input type='radio' id='rcc' name='idtipodo' value = '2' checked/><span class='labela'>C.C.</span>";
						}
					}
					else {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rcc' name='idtipodo' value = '2' disabled='disabled' /><span class='labela'>C.C.</span>";
						}
						else{
							echo "<input type='radio' id='rcc' name='idtipodo' value = '2' /><span class='labela'>C.C.</span>";
						}
					}
					if ($row['idtipodo']==3) {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rce' name='idtipodo' value = '3' checked disabled='disabled'/><span class='labela'>C.E.</span>";
						}
						else{
							echo "<input type='radio' id='rce' name='idtipodo' value = '3' checked /><span class='labela'>C.E.</span>";
						}
					}
					else {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='rce' name='idtipodo' value = '3' disabled='disabled' /><span class='labela'>C.E.</span>";
						}
						else{
							echo "<input type='radio' id='rce' name='idtipodo' value = '3' /><span class='labela'>C.E.</span>";
						}
					}
					?>
				<input type="text" id="ndoc" name="idnitcc" value = <?php echo $row['idnitcc']?> class="texta" <?php bloquearCaratula($idusuario); ?> />
				<span class="labela">DV</span>
				<input type="text" id="ndv" name="iddv" value = <?php echo $row['iddv']?> class="textb" <?php bloquearCaratula($idusuario); ?> />
					<?php
					if ($row['idre']==1) {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='mat1' name='idre' value='1' checked disabled='disabled'/><span class='labela'>Inscrip./Matr.</span>";
						}
						else{
							echo "<input type='radio' id='mat1' name='idre' value='1' checked /><span class='labela'>Inscrip./Matr.</span>";
						}
					}
					else {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='mat1' name='idre' value='1' disabled='disabled' /><span class='labela'>Inscrip./Matr.</span>";
						}
						else{
							echo "<input type='radio' id='mat1' name='idre' value='1' /><span class='labela'>Inscrip./Matr.</span>";
						}
					}
					if ($row['idre']==2) {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='mat2' name='idre' value='2' checked disabled='disabled' /><span class='labela'>Renovaci&oacute;n</span>";
						}
						else{
							echo "<input type='radio' id='mat2' name='idre' value='2' checked /><span class='labela'>Renovaci&oacute;n</span>";
						}
					}
					else {
						if ((substr($idusuario,0,1)!='F')&&(substr($idusuario,0,4)!='CO99')&&(substr($idusuario,0,4)!='CR99')){
							echo "<input type='radio' id='mat2' name='idre' value='2' disabled='disabled' /><span class='labela'>Renovaci&oacute;n</span>";
						}
						else{
							echo "<input type='radio' id='mat2' name='idre' value='2' /><span class='labela'>Renovaci&oacute;n</span>";
						}
					}
					?>
                <span class="labela">C&aacute;mara</span>
				<input type="text" id="cam" name="idcamara" class="textc" value="<?php echo $row['idcamara'] ?>" <?php bloquearCaratula($idusuario); ?> />
                <span class="labela">Incripci&oacute;n/Matricula</span>
				<input type="text" id="reg" name="idmatri" class="texta" value="<?php echo $row['idmatri'] ?>" <?php bloquearCaratula($idusuario); ?> />
				</fieldset>
      
				<fieldset class="marcos">
                <legend class="leyenda">Ubicaci&oacute;n y Datos Generales</legend>
                <div class="labelcara">Raz&oacute;n Social:</div>
				<input type="text" class="textolargo" id="rs" name="idproraz" value="<?php echo trim($row['idproraz']) ?>" <?php bloquearCaratula($idusuario); ?>/><br>
				<div class="labelcara">Nombre Comercial:</div>
				<input type="text" class="textolargo" id="nc" name="idnomcom" value="<?php echo trim($row['idnomcom']) ?>" <?php bloquearCaratula($idusuario); ?>/>
				<div class="labelcara">SIGLA:</div>
				<input type="text" class="textocorto" id="sig" name="idsigla" value="<?php echo trim($row['idsigla']) ?>" <?php bloquearCaratula($idusuario); ?>/><br>
				<div class="labelcara">Domicilio Ppal./Direc. Gerencia:</div>
				<input type="text" class="textolargo" id="dire" name="iddirecc" value="<?php echo trim($row['iddirecc']) ?>" <?php bloquearCaratula($idusuario); ?>/><br>
				<div class="labelcara">Departamento:</div>
				<?php
					$condep = "SELECT DISTINCT dpto, ndpto FROM eas_divipola";					
					$resdep = mysqli_query($con, $condep);
				?>
				<select class="textop" name="iddepto" id="ldep" onChange="buscaMuni(this.value, 'idmpio', 'lmun', 'contmuni1'); return false;" <?php bloquearCaratula($idusuario); ?>>
				<?php	while ($lidep=mysqli_fetch_array($resdep)) {
							if ($lidep['dpto'] == $row['iddepto']) {
								echo "<option value='" . $lidep['dpto'] . "' selected='selected'>" . $lidep['ndpto'] . "</option>";
							}
							else {
								echo "<option value='" . $lidep['dpto'] . "'>" . $lidep['ndpto'] . "</option>";
							}
						}
				?>
				</select>
				<div class="labelcara">Municipio:</div>
				<?php
					$conmun = "SELECT muni, nmuni FROM eas_divipola WHERE dpto=" . $row['iddepto'];
					$resmun = mysqli_query($con, $conmun);
				?>
				<div id="contmuni1">
				<select class="textop" name="idmpio" id="lmun" <?php bloquearCaratula($idusuario); ?>>
				<?php	while ($limun=mysqli_fetch_array($resmun)) {
							if ($limun['muni'] == $row['idmpio']) {
								echo "<option value='" . $limun['muni'] . "' selected='selected'>" . $limun['nmuni'] . "</option>";
							}
							else {
								echo "<option value='" . $limun['muni'] . "'>" . $limun['nmuni'] . "</option>";
							}
						}
				?>
				</select>
				</div>
				<br/><br/>
                <div class="labelcara">Tel&eacute;fono:</div>
				<input type="text" name="idtel" id="ntele" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idtel'] ?>" class="texty" <?php bloquearCaratula($idusuario); ?>/>
				<div class="labelcara">Fax:</div>
				<input type="text" class="texty" name="idfax" id="nfax" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idfax'] ?>" <?php bloquearCaratula($idusuario); ?>/><br /><br />
				<div class="labelcara">Email Empresa:</div>
				<input type="text" name="idcorreo" id="idmail" class="textolargo" value="<?php echo $row['idcorreo'] ?>" <?php bloquearCaratula($idusuario); ?>/>
				<div class="labelcara">Sitio Web:</div>
				<input type="text" name="idweb" id="idweb" class="textolargo" value="<?php echo $row['idweb'] ?>" <?php bloquearCaratula($idusuario); ?>/><br/>
                <div class="labelcara">Direcci&oacute;n Notificaci&oacute;n:</div>
				<input type="text" class="textolargo" id="dirn" name="iddino" value="<?php echo trim($row['iddino']) ?>" <?php bloquearCaratula($idusuario); ?>/><br/>
                <div class="labelcara">Departamento Notificaci&oacute;n:</div>
				<?php
					$condepn = "SELECT DISTINCT dpto, ndpto from eas_divipola";
					$resdepn = mysqli_query($con, $condepn);
				?>
				<select class="textop" name="iddepton" id="ldepn" onChange="buscaMuni(this.value, 'idmpion', 'lmunn', 'contmuni2'); return false;" <?php bloquearCaratula($idusuario); ?>>
				<?php	while ($lidepn=mysqli_fetch_array($resdepn)) {
							if ($lidepn['dpto'] == $row['iddepton']) {
								echo "<option value='" . $lidepn['dpto'] . "' selected='selected'>" . $lidepn['ndpto'] . "</option>";
							}
							else {
								echo "<option value='" . $lidepn['dpto'] . "'>" . $lidepn['ndpto'] . "</option>";
							}
						}
				?>
				</select>
				<div class="labelcara">Municipio Notificaci&oacute;n:</div>
				<?php
					$conmunn = "SELECT muni, nmuni FROM eas_divipola WHERE dpto=" . $row['iddepton'];
					$resmunn = mysqli_query($con, $conmunn);
				?>
				<div name="divmuni2" id="contmuni2">
				<select class="textop" name="idmpion" id="lmunn" <?php bloquearCaratula($idusuario); ?>>
				<?php	while ($limunn=mysqli_fetch_array($resmunn)) {
							if ($limunn['muni'] == $row['idmpion']) {
								echo "<option value='" . $limunn['muni'] . "' selected='selected'>" . $limunn['nmuni'] . "</option>";
							}
							else {
								echo "<option value='" . $limunn['muni'] . "'>" . $limunn['nmuni'] . "</option>";
							}
						}
				?>
				</select>
				</div><br/><br/>
                <div class="labelcara">Tel&eacute;fono Notificaci&oacute;n:</div>
				<input type="text" class="texty" name="idtelno" id="ntelen" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idtelno'] ?>" <?php bloquearCaratula($idusuario); ?> />
                <div class="labelcara">Fax Notificaci&oacute;n:</div>
				<input type="text" class="texty" name="idfaxno" id="nfaxn" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idfaxno'] ?>" <?php bloquearCaratula($idusuario); ?> /><br/><br/>
				<div class="labelcara">Email Notificaci&oacute;n:</div>
				<input type="text" name="idcorreono" id="idmailn" class="textolargo" value="<?php echo  $row['idcorreono'] ?>" <?php bloquearCaratula($idusuario); ?> />
                <!-- <div class="labelcara">Web Notificaci&oacute;n:</div><input type="text" name="idwebno" id="idwebn" class="textolargo" value="<?php //echo $row['idwebno'] ?>" /> -->
                <?php
                                        /**
                                         * Esta opcion queda habilitada para grabar datos generales solo para los administradores.
                                         */
                                       // if($idusuario=='CO99002' OR $idusuario=='CO99001')
                							if (($region == 99 ) AND ($tipousu == "CO")){
                                            ?>
                                            <div style="width: 85%; position: relative; left: 7.5%; top: 2px">
                                                <input type="button" name="grabacaraCoordinador" id="idgrabacaraCoordinador" class="botoncara" value="Grabar Datos Generales" title="Grabar Datos Generales" <?php echo "onClick='grabaCaraCoordinador(" . '"' . $row['idnoremp'] . '", ' . $actividad . '); return false;' . "'" ?> />
                                                <span id="respuestaDatosGenerales" style="position: relative; float: left; font-family: verdana; font-size: 10px; color: #FF0000"></span><br /><br />
                                            </div>
                                            <?php
                                            }
                                        ?>
				</fieldset>
				
				<fieldset class="marcos">
                                    <legend class="leyenda">Tipo de Organizaci&oacute;n y Fecha de Constituci&oacute;n</legend>
                                    <div class="labelcara">Tipo de Organizaci&oacute;n:</div>
					<?php
					$conorg = "SELECT codigo, nombre FROM eas_organiza";
					$resorg = mysqli_query($con, $conorg);
					?>
					<select class="textop" name="idoj" id="idlorg" onChange="muestraOtro('oj');" >
					<?php
					while ($linorg=mysqli_fetch_array($resorg)) {
						if ($linorg['codigo'] == $row['idoj']) {
							echo "<option value='" . $linorg['codigo'] . "' selected='selected'>" . $linorg['nombre'] . "</option>";
						}
						else {
							echo "<option value='" . $linorg['codigo'] . "'>" . $linorg['nombre'] . "</option>";
						}
					}
					if ($row['idoj'] == "99.1") {
						$nclase1 = "labelcara3";
						$tmuestra1 = "block";
					}
					else {
						$nclase1 = "labelcara3h";
						$tmuestra1 = "none";
					}
					?>
					</select>
					<?php
					echo "<div class='" . $nclase1 . "' id='divoj'>Especifique Organizaci&oacute;n: </div>";
					echo "<input style='float: left; width: 40%; display: " . $tmuestra1 . "' type='text' name = 'nidoj' id='idnido' value='" .  $row['nidoj'] . "' />";
					?>
					<div class="labelcara3">Fecha Constituci&oacute;n</div>
					<div style="float: left; width: 30%">
						<div class="labelcara3">Desde: </div>
                                                <input type="text" name="idfcd" id="idfechai" class="textfecha" value="<?php echo $row['idfcd'] ?>" />
						<div class="labelcara3">Hasta:</div>
						<input type="text" name="idfch" id="idfechah" class="textfecha" value="<?php echo $row['idfch'] ?>" />
                                                <span style="color: #FF0000; font-family: arial; font-size: 9px; font-weight: normal; padding: 2px">Formato Fecha : AAAA-MM-DD (A&ntilde;o-Mes-D&iacute;a) Ej: 1987-11-25</span><br />
					</div>
				</fieldset>
				
				<!-- Div para agregar actividades -->
				<div id="cntlista" style="position: absolute; top: 63%; left: 5%; width: 90%; height: 30%; display: none">
					<div id="titlista" style="width: 99.8%; background-color: #FF9900; font-family: arial; font-size: 10px; font-weight: bold; padding: 3px">
						<span style="width: 50%">Seleccione la actividad a adicionar e indique el porcentaje de la misma</span>
						<a href="#" style="position: absolute; right: 3px; color: #000" onClick="ocultar(); return false;">Cerrar[X]</a>
					</div>
					<div id="listaacti" style="width: 98%; height: 70%; overflow: auto; padding: 15px; margin-bottom: 5px; background-color: #F8F8FF">
						<?php
							
						
							if ($periodo<2013){							
								$conacti = "SELECT * FROM eas_ciiu3com WHERE LENGTH(TRIM(codigo))>3 ORDER BY codigo";
							}	
							else{ 
								$conacti = "SELECT * FROM eas_ciiu4com WHERE LENGTH(TRIM(codigo))>3 ORDER BY codigo";
							}
							$resacti = mysqli_query($con, $conacti);
							echo "<table>";
							$ncolor = "#CCCCCC";
							while($linkacti=mysqli_fetch_array($resacti)) {
								if ($ncolor == "#CCCCCC") {
									$ncolor = "#FFF";
								}
								else {
									$ncolor = "#CCCCCC";
								}
								echo "<tr style='background-color: " . $ncolor . "; font-family: arial; font-size: 12px'>";
								echo "<td>". $linkacti["codigo"]  . "</td>";
								echo "<td>". $linkacti["descrip"] . "</td>";
								$idin = "por" . $linkacti["codigo"];
								echo "<td><input type='text' name='porceing' id='" . $idin . "' class='textx' value='0' /></td>";
								$inputid = 'document.getElementById("' . $idin . '").value';
								echo "<td><a id=" . $linkacti["codigo"] . " href='#' onClick=\"grabaActi(this.id, '" . $row["idnoremp"] . "', '" . $idin . "', '".$periodo."' ); return false;\" ><img src='../../images/45.png' border='0' /></a></td>";
								echo "</tr>";
							}
							echo "</table>";
						?>
					</div>
				</div>
				<!--  -->
				
				<fieldset class="marcos">
                    <legend class="leyenda">Composici&oacute;n del Capital Social y Estado Actual</legend>
                    <div class="labelcara">Nacional P&uacute;blico:</div>
					<input type="text" name="ccspun" id="idnalpub" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['ccspun'] ?>" />
					<div class="labelcara3">Nacional Privado:</div>
					<input type="text" name="ccsprn" id="idnalpr" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['ccsprn'] ?>" />
                                        <div class="labelcara3">Extranjero P&uacute;blico:</div>
					<input type="text" name="ccspue" id="idexpub" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['ccspue'] ?>" />
					<div class="labelcara3">Extranjero Privado:</div>
					<input type="text" name="ccspre" id="idexpr" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['ccspre'] ?>" />
					<div class="labelcara3">Estado Actual:</div>
					<?php
						$conestado = "SELECT codigo, estado FROM eas_estadoact";
						$resconest = mysqli_query($con, $conestado, );
					?>
					<select class="textop" name="ideae" id="idestad" onChange="muestraOtro('es')">
					<?php
						while ($linestad=mysqli_fetch_array($resconest)) {
							if ($linestad['codigo'] == $row['ideae']) {
								echo "<option value=" . $linestad['codigo'] . " selected>" . $linestad['estado'] . "</option>";
							}
							else {
								echo "<option value=" . $linestad['codigo'] . ">" . $linestad['estado'] . "</option>";
							}
						}
						if ($row['ideae'] == 7) {
							$nclase2 = "labelcara";
							$tmuestra2 = "block";
						}
						else {
							$nclase2 = "labelcarah";
							$tmuestra2 = "none";
						}
					?>
					</select>
					<?php
					echo "<div class='" . $nclase2 . "' id='dives'>Especifique Estado Actual de la Empresa:</div>";
					echo "<input style='float: left; width: 40%; display: " . $tmuestra2 . "' type='text' name = 'nomeae' id='idnomeae' value='" . $row['nomeae'] . "' />";
					?>
				</fieldset>
      
				<fieldset class="marcos">
                    <legend class="leyenda">N&uacute;mero de Establecimientos que conforman la empresa de acuerdo con la actividad econ&oacute;mica</legend>
					<div class="labelcara">Agropecuarios:</div>
					<input type="text" name="idag" id="idagr" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idag'] ?>" />
					<div class="labelcara3">Mineros:</div>
					<input type="text" name="idnmi" id="idmin" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnmi'] ?>" />
					<div class="labelcara3">Manufactureros:</div>
					<input type="text" name="idnma" id="idmanu" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnma'] ?>" />
                    <div class="labelcara3">Servicios P&uacute;blicos:</div>
					<input type="text" name="idnsp" id="idspu" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnsp'] ?>" />
					<div class="labelcara3">Const. y Obras Civiles:</div>
					<input type="text" name="idncoc" id="idcons" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idncoc'] ?>" />
					<div class="labelcara3">Comerciales:</div>
					<input type="text" name="idnulc" id="idcom" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnulc'] ?>" /><br><br>
					<div class="labelcara">Rest. y Hoteles:</div>
					<input type="text" name="idnes" id="idresh" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnes'] ?>" />
					<div class="labelcara3">Transp. y Almacenamiento:</div>
					<input type="text" name="idntp" id="idtya" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idntp'] ?>" />
                    <div class="labelcara3">Comunicaci&oacute;n y Correo:</div>
					<input type="text" name="idncomu" id="idcyc" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idncomu'] ?>" />
					<div class="labelcara3">Financ. y Otros Serv:</div>
					<input type="text" name="idnfs" id="idfin" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnfs'] ?>" />
					<div class="labelcara3">Servicios Comunales:</div>
					<input type="text" name="idnsc" id="idserc" class="textx" onBlur="checkNumCara(this.id, this.value); return false;" value="<?php echo $row['idnsc'] ?>" />
				</fieldset>
				
				<!--<fieldset class="marcos">
                    <legend class="leyenda">N&uacute;mero de departamentos en los que la empresa cuenta con establecimientos de servicios</legend>
					<input type="text" name="idnuls" id="idnuls" class="textx" value="<?php echo $row['idnuls'] ?>" />					
				</fieldset>-->
				
				<fieldset id="idactiv" class="marcos">
					<legend class="leyenda">
						Actividades Econ&oacute;micas  <span style="font-weight: lighter; font-size: 10px">(describa en orden de importancia las principales)</span>
						&nbsp;&nbsp;&nbsp;
						<a style="text-align: right; color: #003300; font-size: 14px;" href="#" onClick="muestraActi(); return false;">[+ Adicionar Actividades]</a>
					</legend>
					<div id="cntacti" style="width: 100%; height: 100%;">
					<?php
						
						if ($periodo <=2012){
							$conacti = "SELECT a.*, b.descrip FROM eas_actiemp a, eas_ciiu3com b WHERE a.idnoremp = " . $row['idnoremp'] . " AND periodo='".$periodo."' AND b.codigo = a.actividad";						
						}
						else{
							$conacti = "SELECT a.*, b.descrip FROM eas_actiemp a, eas_ciiu4com b WHERE a.idnoremp = " . $row['idnoremp'] . " AND periodo='".$periodo."' AND b.codigo = a.actividad";
						}
						
					    $idnoremp = $row["idnoremp"];
						$resacti = mysqli_query($con, $conacti, );
						$contador = mysqli_num_rows($resacti);
						if (mysqli_num_rows($resacti) > 0) {
							echo "<table style='width: 100%'>";
							while ($lineacti = mysqli_fetch_array($resacti)) {
								echo "<tr>";
								$idacti = trim($lineacti['actividad']);
								echo "<td style='width: 4%; text-align: center; font-family: arial; font-size: 10px'>";
								$funcion = "onClick=\"borraActi( this.id, '" . $row['idnoremp'] . "' , '".$periodo."'  ); return false;\">";
								echo "<a href='#' id='" . $idacti . "' style='color: #000' title='Eliminar esta Actividad' " . $funcion . "[X]</a>";
								echo "</td>";
								echo "<td style='width: 4%; font-family: arial; font-size: 10px; color: #000'>";
								echo $lineacti['actividad'];
								echo "</td>";
								echo "<td style='width: 92%; font-family: arial; font-size: 10px; color: #000'>";
								echo $lineacti['descrip'];
								echo "</td>";
								echo "<td style='width: 92%; font-family: arial; font-size: 10px; color: #000' align='right'>";
								echo $lineacti['porceing'];
								echo '<input type="hidden" id="hddActi'.$idacti.'" name="hddActi" value="'.$lineacti['porceing'].'"/>';
								echo "</td></tr>";
							}
							echo "</table>";
							echo '<input type="hidden" id="contadorAct" name="contadorAct" value="'.$contador.'"/>';
						}
						
					?>
					</div>
				</fieldset>
				
				
				<!-- 9. PERIODO DE OPERACION DE LA EMPRESA -->
				<fieldset class="marcos">
					<legend class="leyenda">Periodo de Operaci&oacute;n de la Empresa</legend>
			        <div class="labelcara">A&ntilde;o de inicio de operaciones de la empresa: <span class="ui-icon ui-icon-info" title="Se refiere al a�o en el cual la empresa inicia el desarrollo de su actividad. Se aclara que el a�o de iniciaci�n de operaciones no debe modificarse por cambio de propietario, raz�n social, administrador u operador. Se debe dejar el a�o de iniciaci�n seg�n el hist�rico."></span></div>
			        
			        
					<input type="text" id="idrep" name="idaio" class="textx" value="<?php echo $row['idaio'] ?> " />
			        <div class="labelcara">Meses de operaci&oacute;n de la actividad de servicios en el a&ntilde;o <?php echo $periodo?>:</div>
					<input type="text" id="iddil" name="idmeop" class="textx" onBlur='checkmes(this.value); return false;' value="<?php echo $row['idmeop'] ?> " />
			        <div class="labelcarah" id="idlmes">Si los meses de operaci&oacute;n para el <?php echo $periodo?> fueron inferiores a 12 meses, seleccione la causa:</div>
					<select class="textoph" name="idcapf" id="idmesinf" onChange="muestraOtro('mes');">
					<?php	for ($i=0; $i<8; $i++) {
								if ($row['idcapf'] == $i) {
									echo "<option value='" . $i . "' selected>" . $causas[$i] . "</option>";
								}
								else {
									echo "<option value='" . $i . "'>" . $causas[$i] . "</option>";
								}
							}
					?>
					</select><br/>
					<?php	if ($row['idcapf'] == 6) {
								$nclase1 = "labelcara3";
								$tmuestra1 = "block";
							}
							else {
								$nclase1 = "labelcara3h";
								$tmuestra1 = "none";
							}
							echo "<div class='" . $nclase1 . "' id='divmes'>Especifique Causa: </div>";
							echo "<input style='float: left; width: 40%; display: " . $tmuestra1 . "' type='text' name = 'ob_noved' id='idmesmen' value='" .  $row['ob_noved'] . "' />";
					?>
				</fieldset>
				
				<?php //DANIEL M. DIAZ F.
					  //Abril 05 de 2013
					  //Se agrega una pregunta adicional a la caratula para todos los formularios a partir del periodo 2012 (Se elimino el detalle de la caratula)
					  if ($periodo > 2011){  ?>
					  	<fieldset class="marcos">
					  		<legend class="leyenda">Durante el a&ntilde;o, la empresa realiz&oacute; operaciones de comercio exterior de:</legend>
					  		<div class="labelcara">Bienes&nbsp;<input type="radio" id="radBienes" name="opcomex" value="1" <?php if ($row["opcomex"]==1){ echo 'checked="checked"'; } ?>></div>
					  		<div class="labelcara3">Servicios&nbsp;<input type="radio" id="radServicios" name="opcomex" value="2" <?php if ($row["opcomex"]==2){ echo 'checked="checked"'; } ?>></div>
					  		<div class="labelcara3">Bienes y Servicios&nbsp;<input type="radio" id="radBienesyServ" name="opcomex" value="3" <?php if ($row["opcomex"]==3){ echo 'checked="checked"'; } ?>></div>
					  		<div class="labelcara3">Ninguno&nbsp;<input type="radio" id="radNinguno" name="opcomex" value="4" <?php if ($row["opcomex"]==4){ echo 'checked="checked"'; } ?>></div>
					  	</fieldset>
				<?php } ?>
				
				
				<!-- Modificaci�n para agregar campos de Generacion Energetica 
				     Solo aplica a partir del periodo de recoleccion 2014. 
				     NO aplica para periodos anteriores al 2014. 
				 -->
				<?php // DEBE CAMBIARSE EL PERIODO A 2014 !!!
					  if (($periodo>2013)&&($periodo<2016)){
				?>	
							<fieldset class="marcos">
							<legend class="leyenda">Generaci&oacute;n de Energ&iacute;a por Parte de la Empresa</legend>
				    		<div style="float: left; font-family: arial; font-size: 9px; margin-right: 3px; text-align: right; padding-top: 4px;" >La empresa realiz&oacute; autogeneraci&oacute;n y/o cogeneraci&oacute;n de energ&iacute;a el&eacute;ctrica durante el a&ntilde;o, bien sea para consumo interno o para la venta.</div>
						    <br/>
							<table width="100%">
							<tr>
					  			<td style="float: left; font-family: arial; font-size: 9px; margin-right: 3px; text-align: right; padding-top: 4px;">1. Auto-generaci&oacute;n: &nbsp;&nbsp; 
					      		<select id="AUGENE" name="AUGENE">
					      			<option value="-">...</option>
					      			<option value="1" <?php echo (isset($row["augene"]) && $row["augene"]==1)?"selected='selected'":""; ?>>Si</option>
					      			<option value="0" <?php echo (isset($row["augene"]) && $row["augene"]==0)?"selected='selected'":""; ?>>No</option>
					      		</select>
					  			</td>					  
							</tr>
							<tr>
					  			<td style="float: left; font-family: arial; font-size: 9px; margin-right: 3px; text-align: right; padding-top: 4px;">2. Co-Generaci&oacute;n: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					      		<select id="COGENE" name="COGENE">
					      			<option value="-">...</option>
					      			<option value="1" <?php echo (isset($row["cogene"]) && $row["cogene"]==1)?"selected='selected'":""; ?>>Si</option>
					      			<option value="0" <?php echo (isset($row["cogene"]) && $row["cogene"]==0)?"selected='selected'":""; ?>>No</option>
					      		</select>
					  			</td>					  
							</tr>
							</table>					
							</fieldset>				
				<?php }
                      else if ($periodo >= 2016){ 					  
							//Formulario de Generacion de Energia para el periodo 2016 (Ver el archivo gen_energia.php)
							$energia = new GeneraEnergia();
							$energia->generacionEnergiaFORM($periodo, $row["idnoremp"]); 
					  } 
				?>				
				
				<!-- 11. DATOS DEL INFORMANTE -->
				<fieldset class="marcos">
					<legend class="leyenda">Datos del Informante</legend>
					<div class="labelcara">Representante Legal:</div>
					<input type="text" id="idrep" name="repleg" class="textocorto" value="<?php echo $row['repleg'] ?> " /><br>
					<div class="labelcara">Persona que diligencia:</div>
					<input type="text" id="iddil" name="responde" class="textocortob" value="<?php echo $row['responde'] ?> " />
                    <div class="labelcara2">Tel&eacute;fono:</div>
					<input type="text" id="idteldil" name="teler" class="texty" value="<?php echo $row['teler'] ?> " />
					<div class="labelcara2">Extensi&oacute;n:</div>
					<input type="text" id="ext" name="ext" class="texty" value="<?php echo $row['ext'] ?> " /><br><br>
					<div class="labelcara">Email persona que diligencia:</div>
					<input type="text" id="idemres" name="emailr" class="textocorto" value="<?php echo $row['emailr'] ?> " />					
				</fieldset>
				
				<?php
					if ($tipousu == 'CO') {
						if ($lineactl['estado'] == 0 AND $region != 99) {
							echo "<fieldset class='marcos'>";
							echo "<legend class='leyenda'>Distribuci&oacute;n</legend>";
							echo "<div class='labelcara'>Fecha de distribuci&oacute;n:</div>";
							echo "<input type='text' name='fecdist' id='idfecdist' class='textfecha' value= " . $lineactl['fecdist'] . " />";
							echo "<a href=#' id='idactdist' title='Actualizar Fecha de Distribuci&oacute;n' onClick='grabaDist(" . $periodo . ", " . $row['idnoremp'] . ");'><img style='border: 0px' src='../../images/45.png'></a>";
							echo "</fieldset>";
						}
					}
				?>
				
				<fieldset class="marcosnv" id="marcociiu">
					<legend class="leyenda">Regional/Sede - CIIU:</legend>
					<?php
						$conreg = "SELECT codis, nombre FROM eas_regionales WHERE codis != 99 ORDER BY codis";
						$resreg = mysqli_query($con, $conreg, );
					?>
					<div class="labelcara">Regional/Sede:</div>
					<select class="textop" name="regional" id="idreg">
					<?php
					echo "<option value='0' selected>*** SELECCIONE REGIONAL/SEDE ***</option>";
					while ($lreg=mysqli_fetch_array($resreg)) {
						echo "<option value='" . $lreg['codis'] . "'>" . $lreg['nombre'] . "</option>";
					}
					?>
					</select>
					<?php
						$conciiu = "SELECT idact, descripcion FROM eas_ciiu WHERE LENGTH(idact) > 3 ORDER BY idact";
						$resciiu = mysqli_query($con,$conciiu);
					?>
					<div class="labelcara">CIIU:</div>
					<select class="textop" name="ciiu3" id="idciiu">
					<?php
					echo "<option value='0' selected>*** SELECCIONE LA ACTIVIDAD DE LA EMPRESA ***</option>";
					while ($lciiu=mysqli_fetch_array($resciiu)) {
						echo "<option value='" . $lciiu['idact'] . "'>" . $lciiu['idact'] . " - " . $lciiu['descripcion'] . "</option>";
					}
					?>
					</select>
				</fieldset>
			</form>
			
			<div style="width: 85%; position: relative; left: 7.5%; top: 2px">
				<input type="button" name="inscara" id="insecara" class="botoncarai" value="Insertar" title="Insertar Nueva Empresa" <?php echo "onClick='insertaCara(); return false;'"  ?> />
                
                <!-- BOTON PARA GUARDAR EL FORMULARIO. -->
                <input type="button" name="grabacara" id="idgrabacara" class="botoncara" value="Grabar" title="Actualizar Informaci&oacute;n" <?php echo "onClick='grabaCara(" . '"' . $row['idnoremp'] . '", ' . $actividad . ', ' . $periodo . ');' . "'" ?> />
				
				<span id="respuesta" style="position: relative; float: left; font-family: verdana; font-size: 10px; color: #FF0000"></span><br /><br />
				<?php //DANIEL M. DIAZ F.
					  //Abril 05 de 2013
					  //Se Elimina el detalle de la caratula. Se elimina el Bot�n "Ir a Estructura de la empresa", para los periodos superiores al 2012
					   if ($periodo <= 2011){ 
							echo "<span style='position: absolute; right: 2px; font-family: arial; font-size: 10px'><b>Ir a:</b>";
							echo "<a class='meresu' href='eas_anexdir.php?idemp=" . $row['idnoremp']  . "&nombremp=" . $row['idnomcom'] . "&periodo=" . $periodo . "'>Estructura de la Empresa</a>";
							echo "</span>";
					   }		
				?>
			</div>
<?php	}
		else {  //NO EXISTE
				echo "<span id='respuesta' style='position: relative; float: left; font-family: verdana; font-size: 12px; color: #FF0000'>";
				echo "NO se encontraron coincidencias";
				echo "</span>";
		}
?>

<!--        <div id="div_totalPantalla" class="fondoBloqueo">-->
<div id="empadic" style="width: 85%; position: absolute; left: 7.5%; top: 15%; background-color: #FFF; border: solid 1px #00008B; display: none">
	<div style="width: 99.8%; font-family: arial; font-size: 11px; background-color: #00008B; color: #FFFFFF; padding: 3px">
		<span><b>ADICION DE NUEVAS EMPRESAS</b></span>
		<a href="#" style="position: absolute; right: 2px; font-family: arial; font-size: 10px; color: #FF0000" onClick="cierraAdic('empadic'); return false;">Cerrar [X]</a>
	</div>

  	<div style="width: 100%"><br/>
    	<span  class="labelcara" >N&uacute;mero de Orden :</span>
    	<input type="text" id="nord" name="idnoremp" class="texta" >
    	<input type='radio' id='enit' name='idtipodo' /> <span class='labela'>Nit</span>
    	<input type='radio' id='ecc' name='idtipodo' />  <span class='labela'>C.C.</span>
     	<input type='radio' id='ece' name='idtipodo' />  <span class='labela'>C.E.</span>
		<input type="text" id="nitc" name="nitc" class="texta" />
		<span class="labela">DV</span>
		<input type="text" id="endv" name="endv" class="textb" /><br>
		<?php
			$consede = "SELECT dpto, codis, nombre FROM eas_regionales WHERE codis < 99 ORDER BY codis";
			$ressede = mysqli_query($con, $consede);
		?>
		<div class="labelcara" >Sede de la Empresa:</div>
			<select style="width: 20%" class="textop" name="sede" id="sede">
			<?php
				echo "<option value='0' selected>*** SEDE EMPRESA ***</option>";
				while ($lsede=mysqli_fetch_array($ressede)) {
					echo "<option value='" . $lsede['codis'] . "'>" . $lsede['codis'] . " - " . $lsede['nombre'] . "</option>";
					echo "<br><br>";
				}
			?>
			</select><br>
			<?php
				$conciiu = "SELECT idact, descripcion FROM eas_ciiu WHERE LENGTH(idact) > 3 ORDER BY idact";
				$resciiu = mysqli_query($con, $conciiu);
			?>
			<div class="labelcara" >Actividad Econ&oacute;mica CIIU:</div>
				<select style="width: 75%" class="textop" name="ciiu3" id="ciiu">
				<?php	echo "<option value='0' selected>*** SELECCIONE LA ACTIVIDAD DE LA EMPRESA ***</option>";
						while ($lciiu=mysqli_fetch_array($resciiu)) {
							echo "<option value='" . $lciiu['idact'] . "'>" . $lciiu['idact'] . " - " . $lciiu['descripcion'] . "</option>";
							echo "<br>";
						}
				?>
				</select><br/>
				<span class="labelcara">Nombre Comercial :</span>
    			<input style="width: 75%" type='text' id='nomcom' tabindex="1"  onFocus='this.select();' />
    			<br/>
    			<?php echo "<input type='button' class='botonuni' tabindex='7' value='Grabar Nueva Empresa' onClick='grabarEmp(\"nord\", \"enit\", \"ecc\", \"ece\", \"nitc\", \"endv\", \"ciiu\", \"nomcom\", \"sede\");' />"; ?>
  			</div>
        </div>
        <!--  -->
	</div>
</div>
</body>
</html>
