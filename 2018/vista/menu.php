<?php
	
	

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

	header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

	
	session_start();
	/*
	if (session_id() == "") {
		session_start();
	}
	*/
	
//	var_dump ($_SESSION);

	if (isset($_SESSION['debug-dane'])) {
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
	}

	// if(isset($_REQUEST['periodoSeleccion']) && $_REQUEST['periodoSeleccion'] != 0){
    // 	$_SESSION['anoproc'] = $_REQUEST['periodoSeleccion'];
    // }
	
	$_SESSION['anoproc'] = '2018';
    $ident_usu = $_SESSION['idusu'];
	$cod_regi = $_SESSION['region'];
	$anoproc = $_SESSION['anoproc'];
	
	if (isset($_GET['atras'])) {
		unset($_GET['nomest']);
		unset($_GET['atras']);
	}
	
	$periodo= $_SESSION['anoproc'];
	$descestado = "";
	$numeroest = 0;
	$idemp = $_SESSION['numero'];
	include '../../servicio/eas_conecta.php';
	
	if (isset($_GET["idemp"])) { //No esta definido el numero de empresa (Soy el coordinador o el critico)
		$empresa = $_GET["idemp"];
	}
	else if ($idemp != 0){ //Esta definido el numero de empresa (Soy la fuente)
		$empresa = $_SESSION["numero"];
	}
	else{
		$empresa = $_SESSION["numero"];
	} 
	
	
	//var_dump($empresa);
	/**
	 * Modificaci�n para actividad CIIU4 
	 * @author Daniel M. Diaz F
	 * @since  Julio 01 / 2014  
	 */

	if (($periodo>=2013) && ($empresa!=0)){
		$sql = "SELECT idact, ciiu4
				FROM eas_directorio
				WHERE periodo = $periodo
				AND idnoremp = $empresa";
		
		if (isset($_SESSION["debug-dane"]))
		    echo $sql . "<br>";
		    
		
		$res = mysqli_query($con, $sql);
		
		while($row = mysqli_fetch_array($res)){
			$actividadCIIU4 = $row["ciiu4"];
		}
		$_SESSION["ciiu4"] = $actividadCIIU4;  //Registrar la actividad CIIU4 en la sesion 
	}
	
	
	/**
	 * arreglo del estado de los periodos !!! Q.P
	 * @author Jonathan Esquivel <jresquivelf@dane.gov.co>
	 * @since 23/03/2012
	 */
	
	#$consregctl = "SELECT * FROM eas_regcontrol ORDER BY periodoproc DESC";
	$consregctl = "SELECT * FROM eas_regcontrol ";
	if($cod_regi != 99){
		$consregctl .= "WHERE estadoeam = 0 ";
	}
	$consregctl .= "ORDER BY periodoproc DESC";
	$resregctl = mysqli_query($con, $consregctl );
	$linregctl = mysqli_fetch_array($resregctl);
	$periproc = $linregctl['periodoproc'];

	$conempre = "SELECT periodo, idnoremp, idact, idnomcom FROM eas_directorio WHERE idnoremp = " . $idemp ." AND periodo=" . $periodo ;
	$restable = mysqli_query($con,$conempre);
	$linfu = mysqli_fetch_array($restable);
	switch ($_SESSION['tipou']) {
		case "CO":
			$descmenu = "Men&uacute; Coordinador";
			if (isset($linfu['idnomcom'])) {
				$descmenu = "Men&uacute; Coordinador [" . trim($linfu['idnomcom']) . "]";
			}
			break;
		case "CR":
			$descmenu = "Men&uacute; Cr&iacute;tico - Analista";
			if (isset($linfu['idnomcom'])) {
				$descmenu = "Men&uacute; Cr&iacute;tico - Analista [" . trim($linfu['idnomcom']) . "]";
			}
			break;
		case "TE":
			$descmenu = "Men&uacute; Tem&aacute;tico - Log&iacute;stico";
			if (isset($linfu['idnomcom'])) {
				$descmenu = "Men&uacute; Tem&aacute;tico - Log&iacute;stico [" . trim($linfu['idnomcom']) . "]";
			}
			break;
				
		case "FU":
			$descmenu = trim($linfu['idnomcom']);
			//$conempre = "SELECT periodo, idnoremp, idact, idnomcom FROM eas_directorio WHERE idnoremp = " . $idemp;
			//$conctl = "SELECT *, CASE estado WHEN 0 THEN 'Pendiente' WHEN 1 THEN 'Distribuido' WHEN 2 THEN 'En Digitaci&oacute;n' WHEN 3 THEN 'Formulario Completo'";
			//$conctl .= " END AS letras FROM eas_control WHERE idnoremp=" . $idemp . " AND periodo = ". $periodo;
			
			$conctl = "SELECT *, CASE estado 
			                       WHEN 0 THEN 'Pendiente' 
			                       WHEN 1 THEN 'Distribuido' 
			                       WHEN 2 THEN 'En Digitaci&oacute;n' 
			                       WHEN 3 THEN 'Formulario Completo' 
			                     END AS letras 
			           FROM eas_control 
			           WHERE idnoremp = $idemp 
			           AND periodo = $periodo";			

			//$restable = mysql_query($conempre, $con);
			//$linfu = mysql_fetch_array($restable);
			$resctl = mysqli_query($con, $conctl );

			$conesctl = "SELECT idnoremp, estado FROM eas_control WHERE estado=5 AND idnoremp=" . $idemp . " and periodo = ".$periodo;
			$restactl = mysqli_query($con, $conesctl);
			$numest = mysqli_num_rows($restactl);
			$restctl = mysqli_fetch_array($restactl);

			$linctl = mysqli_fetch_array($resctl);
			$descestado .= " [" . $linctl['letras'] . "]";
			break;
	}
	
	//echo $ciiu3 . "</br>";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>DANE - Encuesta Anual de Servicios - Formulario Electr&oacute;nico</title>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/eas_mopcion.js"></script>
		<link rel = stylesheet href = "../../css/formelec.css" type="text/css">
	</head>
	<body style="background-color: #F8F9FE">	
		<img style="float:left" src="../../images/logo_dane-presidencia.png" width=200 height=50 /><br>	
		<div style="float: left; width: auto">
			<span style="font-family: arial; font-size: 11px; font-weight: bold; color: #000; padding-left: 10px">Encuesta Anual de Servicios - Formulario Electr&oacute;nico - <?php echo $anoproc?></span><br>
			<span style="font-family: arial; font-size: 11px; font-weight: bold; color: #990000; padding-left: 10px"><?php echo $descmenu ?>	</span><span class="estado"><?php echo $descestado ?></span><br>
			<?php 
	
					$ban = "style='display: inline;'";
					if (isset($_SESSION['debug-dane']))
						$ban = "style='display: inline;'";
					
			
					switch ($_SESSION['tipou']) {
						/**
						 * DMDIAZF - Mayo 07 de 2013
						 * CASO PARA EL MODULO DE COORDINADOR. SE CONTRUYE EL MENU PARA EL MODULO DE COORDINADOR
						 */	
						case "CO":	
									$prgindcal = "eas_indcal.php";
									if (!isset($_GET['nomest'])) {
                                    	$sql_periodos = "SELECT * FROM eas_regcontrol ";
										if($cod_regi != 99){
											$sql_periodos .= "WHERE estadoeam = 0 ";
										}
                                    	$sql_periodos .= "ORDER BY periodoproc DESC";
                                    	$periodo_array = mysqli_query($con, $sql_periodos);
			?>                          <div style='padding-left: 10px; padding-top: 1px' id='per'>
                                        <form name="formPeriodo" method="POST" action="menu.php">
                                      	<select name="periodoSeleccion" id="periodoSeleccion" onchange="parent.location.replace('http://localhost/EasgoNuevo/'+$('#periodoSeleccion').val())">
                                        <option value="0">Periodo...</option>
            <?php                       while($arreglo_periodo = mysqli_fetch_array($periodo_array)){
                                        	if($arreglo_periodo['periodoproc']==$_SESSION['anoproc']){
                                            	echo "<option value='".$arreglo_periodo['periodoproc']."' selected>".$arreglo_periodo['periodoproc']."</option>";
                                            }
                                            else{
                                                echo "<option value='".$arreglo_periodo['periodoproc']."'>".$arreglo_periodo['periodoproc']."</option>";
                                            }                                                    
                                        }
           ?>                           </select>
                                        </form>
                                        </div>
			<?php						echo "<div style='padding-left: 10px; padding-top: 1px' id='adm'>";
										if($periodo < 2020 ){
											echo "<a class='menuc' href='eas_caratula.php' target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Directorio</a>";
										}else{
											echo "<a class='menuc' href='2020/eas_caratulaDir.php' target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Directorio</a>";
										}
										if ($_SESSION['tipou'] == 'CO' AND $_SESSION['numero'] == 0) {
											echo "<a class='menuc' href='eas_usuarios.php' target='contenido' onMouseOver='muestraOpcion(\"usu\"); return false;'>Usuarios</a>";
										}
										echo "<a class='menuc' href='eas_buscar.php' target='contenido' onMouseOver='muestraOpcion(\"for\"); return false;'>Formularios</a>";
										echo "<a class='menuc' href='eas_operativo.php' target='contenido' onMouseOver='muestraOpcion(\"oper\"); return false;'>Operativo</a>";
										if ($cod_regi == 99) {
											$prgindcal = "eas_indcaldc.php";
											echo "<a class='menuc' href='eas_capitulos.php' target='contenido' onMouseOver='muestraOpcion(\"desc\"); return false;'>Descargar cap&iacute;tulos</a>";
											echo "<a class='menuc' href='eas_analisisvg.php' target='contenido' onMouseOver='muestraOpcion(\"vg\"); return false;'>An&aacute;lisis</a>";
											echo "<a class='menuc' href='eas_borraemp.php' target='contenido' onMouseOver='muestraOpcion(\"br\"); return false;'>Borrado Empresas</a>";
											//echo "<a class='menuc' href='cuadros.php' target='contenido'>Cuadros</a>";
										}
                    					echo "<a class='menuc' href='" . $prgindcal . "' target='contenido'>Ind. Calidad</a>";
                    					//echo "<a class='menuc' href='reportes-birt.php' target='contenido'>Cuadros de Salida</a>";
                    					/**
                     					* adicion menu administracion
                     					* @author Jonathan Esquivel <jresquivelf@dane.gov.co>
                     					* @since 14/03/2012
                     					*/
                    					if($cod_regi == 99){
											print "<a class=\"menuc\" href=\"eas_admon.php\" target=\"contenido\" onMouseOver=\"muestraOpcion('admon'); return false;\">Administraci&oacute;n</a>";
                    					}
                    					
                    					//echo "<a class=\"menuc\" href=\"reportes2/menurepo.php\" target=\"contenido\">Reportes</a>";
                    					echo "<a class=\"menuc\" href=\"easci/index.php/reportes/index/".$_SESSION["idusu"]."\" target=\"contenido\">Reportes</a>";
                    					/*if ($_SESSION['idusu']=='CO99001'){
                    						echo "<a class='menuc' href='eas_faltainfo.php' target='contenido'>Veri Sistemas</a>";
                    					}*/
										echo "</div>";
										
										
									}
									else {
										echo "<div style='padding-left: 10px; padding-top: 1px' id='con'>";
										$qstrestab = "?idemp=" . $_GET['idemp'] . "&nomest=" . $_GET['nomest'] . "' ";			
										
										if($periodo >= 2018 && $periodo != 2020 ){
											echo "<a class='menuc' href='eas_caratula2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'> Modulo I <p $ban ></p></a>";	
										}elseif($periodo == 2020 ){
											echo "<a class='menuc' href='2020/eas_caratula.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'> Modulo I <p $ban ></p></a>";	
										}else{
											echo "<a class='menuc' href='eas_caratula.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'> Modulo I <p $ban ></p></a>";	
										}
										
										/** MODIFICACION PARA CAMBIAR EL FORMULARIO DEL MODULO II DEPENDIENDO DE LA ACTIVIDAD CIIU4 A PARTIR DEL PERIODO 2013 ***/
										if ($periodo < 2013){
											if ($_GET['idact'] > 8010 AND $_GET['idact'] < 8091) {
												echo "<a class='menuc' href='eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
											else {
												echo "<a class='menuc' href='eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
										}else if($periodo >= 2018 && $periodo != 2020){
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
												echo "<a class='menuc' href='eas_perseduca2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
											else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
												echo "<a class='menuc' href='eas_personal2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
										}elseif($periodo == 2020 ){
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
												echo "<a class='menuc' href='2020/eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
											else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
												echo "<a class='menuc' href='2020/eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
										}else{
											
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
												echo "<a class='menuc' href='eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
											else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
												echo "<a class='menuc' href='eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
											}
										}	
										
										if ($periodo >= 2016 && $periodo <= 2017){
											echo "<a class='menuc' href='eas_ingresos2016.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";
										}else if($periodo >= 2018 && $periodo != 2020 ){
											echo "<a class='menuc' href='eas_ingresos2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";
										}elseif($periodo == 2020 ){
											echo "<a class='menuc' href='2020/eas_ingresos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";
										}else{
											echo "<a class='menuc' href='eas_ingresos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";
										}
										
										if($periodo >= 2018 && $periodo != 2020){
											echo "<a class='menuc' href='eas_activos2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='eas_unidades2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}elseif($periodo == 2020){
											echo "<a class='menuc' href='2020/eas_activos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='2020/eas_unidades.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}else{
											echo "<a class='menuc' href='eas_activos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='eas_unidades.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}
										
										
										if ($periodo<=2011){
											//Se utilza el formulario para TICS antiguo.
											echo "<a class='menuc' href='eas_tics.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
										}
										else if ($periodo>=2012 && $periodo <= 2017){
											//Se utilza el formulario para TICS Nuevo.
											echo "<a class='menuc' href='./eas_tics/ticsn.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
										}else if($periodo == 2018){
											echo "<a class='menuc' href='./eas_tics/ticsn2018.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
										}else if($periodo == 2020){
											echo "<a class='menuc' href='2020/ticsn.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
										}
										
										if($periodo == 2020){
											echo "<a class='menuc' id='modamb 'href='2020/eas_ambiental_proteccion.php" . $qstrestab . "'  target='contenido' onMouseOver='muestraOpcion(\"ambi\"); return false;'>Modulo VII <p $ban ></p></a>";
										}
										
										/****
										else if ($periodo>2012){
											echo "<a class='menuc' href='./eas_tics/eastics_2013/ticsn.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI</a>";	
										}
										****/
										/*****
										if (substr($_GET['idact'], 0, 3) == '642') {
											echo "<a class='menuc' href='eas_telecomunicaciones.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VII</a>";
										}
										******/
										
										//Módulo VII - Módulo NIIF
										//@author dmdiazf
										//@since  12/04/2016										
										
										if($periodo <= 2017){ 
											echo '<a class="menuc" id="mod7" href="./eas_niif.php?idemp=' . $_GET['idemp'] . '&nomest=' . $_GET['nomest']  .'" target="contenido" onMouseOver="muestraOpcion(\'niif\');">Modulo VII <p $ban ></p></a>';	 
										}

										if ($periodo==2019){
											echo "<a class='menuc' href='./eas_tics/ticsn2018.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
											echo "<a class='menuc' id='modamb 'href='eas_ambiental.php" . $qstrestab . "'  target='contenido' onMouseOver='muestraOpcion(\"ambi\"); return false;'>Modulo VII <p $ban ></p></a>";	
										}
										
										
										echo "<a class='menuc' href='menu.php?atras=si' onMouseOver='muestraOpcion(\"mpp\"); return false;'>Men&uacute; ppal</a>";
										
										
										if($periodo >= 2018 && $periodo != 2020 ){
											echo "<a class='menuc' href='eas_diagfor2018.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}elseif($periodo == 2020 ){
											echo "<a class='menuc' href='2020/eas_diagfor.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}else{
											echo "<a class='menuc' href='eas_diagfor.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}
										echo "</div>";
										
									}
									
									break;
									
									
						/**
						 * DMDIAZF - Mayo 07 de 2013
						 * CASO PARA EL MODULO DE CRITICA. SE CONTRUYE EL MENU PARA EL MODULO DE CRITICA
						 */		
						case "TE": 
						case "CR":	if (!isset($_GET['nomest'])) {
                                    	/**
									 	* arreglo del estado de los periodos
									 	* @author Jonathan Esquivel <jresquivelf@dane.gov.co>
									 	* @since 23/03/2012
									 	*/
									
										#sql_periodos = "SELECT * FROM eas_regcontrol ORDER BY periodoproc DESC";
										$sql_periodos = "SELECT * FROM eas_regcontrol WHERE estadoeam = 0 ORDER BY periodoproc DESC";
                                    	$periodo_array = mysqli_query($con, $sql_periodos);
			?>                      	<div style='padding-left: 10px; padding-top: 1px' id='per'>
                                    	<form name="formPeriodo" method="POST" action="menu.php">
                                    	<select name="periodoSeleccion" id="periodoSeleccion" onchange="submit()">
                                    	<option value="0">Periodo...</option>
            <?php                   	while($arreglo_periodo = mysql_fetch_array($periodo_array)){
                                    		if($arreglo_periodo['periodoproc']==$_SESSION['anoproc']){
                                        		echo "<option value='".$arreglo_periodo['periodoproc']."' selected>".$arreglo_periodo['periodoproc']."</option>";
                                        	}
                                        	else{
                                            	echo "<option value='".$arreglo_periodo['periodoproc']."'>".$arreglo_periodo['periodoproc']."</option>";
                                        	}
                                        }
            ?>                         	</select>
                                    	</form>
                                    	</div>
            <?php						echo "<div style='padding-left: 10px; padding-top: 1px' id='con'>";
										if ($cod_regi == 99) {
											if($periodo < 2020 ){
												echo "<a class='menuc' href='eas_caratula.php' target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Directorio</a>";
											}else{
												echo "<a class='menuc' href='2020/eas_caratulaDir.php' target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Directorio</a>";
											}
										}
										else {
											echo "<a class='menuc' href='eas_listacara.php' target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Directorio</a>";
										}
										if ($_SESSION['tipou'] == "CR" AND $cod_regi == 99) {
											//var_dump($_SESSION["tipou"]);
											echo "<a class='menuc' href='eas_buscar.php' target='contenido' onMouseOver='muestraOpcion(\"for\"); return false;'>Formularios</a>";
										}
										else {
											if ($_SESSION["tipou"]=="TE"){
												echo "<a class='menuc' href='eas_buscar.php' target='contenido' onMouseOver='muestraOpcion(\"for\"); return false;'>Formularios</a>";												
											}
											else{
												echo "<a class='menuc' href='eas_listacap.php' target='contenido' onMouseOver='muestraOpcion(\"for\"); return false;'>Formularios</a>";	
											}	
										}
										echo "<a class='menuc' href='eas_operativo.php' target='contenido' onMouseOver='muestraOpcion(\"oper\"); return false;'>Operativo</a>";
										if ($ident_usu == "CR99012" OR $ident_usu == "CR99013" OR $ident_usu == "CR99017" OR substr($ident_usu,0,4)=="CR99" OR substr($ident_usu,0,4)=="TE99") {
											echo "<a class='menuc' href='eas_capitulos.php' target='contenido' onMouseOver='muestraOpcion(\"desc\"); return false;'>Descargar cap&iacute;tulos</a>";
										}
										if ($cod_regi == 99) {
											echo "<a class='menuc' href='eas_analisisvg.php' target='contenido' onMouseOver='muestraOpcion(\"vg\"); return false;'>An&aacute;lisis</a>";
											
											//Muestra el link para los reportes de la segunda version
											//@author Daniel M. D�az
											//@since  30 Septiembre 2014
											//echo "<a class=\"menuc\" href=\"easci/index.php/reportes\" target=\"contenido\">Reportes</a>";
											echo "<a class=\"menuc\" href=\"easci/index.php/reportes/index/".$_SESSION["idusu"]."\" target=\"contenido\" >Reportes</a>";
										}
										
										echo "</div>";
								}
								else {
										echo "<div style='padding-left: 10px; padding-top: 1px' id='con'>";
										$qstrestab = "?idemp=" . $_GET['idemp'] . "&nomest=" . $_GET['nomest'] . "' ";
										if($periodo >= 2018 && $periodo != 2020 ){
											echo "<a class='menuc' href='eas_caratula2018.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Modulo I <p $ban ></p></a>";
										}elseif ($periodo == 2020){
											echo "<a class='menuc' href='2020/eas_caratula.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Modulo I <p $ban ></p></a>";
										}else{
											echo "<a class='menuc' href='eas_caratula.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"cara\"); return false;'>Modulo I <p $ban ></p></a>";
										}
                                        /** MODIFICACION PARA CAMBIAR EL FORMULARIO DEL MODULO II DEPENDIENDO DE LA ACTIVIDAD CIIU4 A PARTIR DEL PERIODO 2013 ***/
                                        if ($periodo < 2013){
                                        	if ($_GET['idact'] > 8010 AND $_GET['idact'] < 8091) {
                                        		echo "<a class='menuc' href='eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        	else {
                                        		echo "<a class='menuc' href='eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        }else if ($periodo >= 2018 && $periodo != 2020){
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
                                        		echo "<a class='menuc' href='eas_perseduca2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        	else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
                                        		echo "<a class='menuc' href='eas_personal2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
										}else if ($periodo == 2020){
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
                                        		echo "<a class='menuc' href='2020/eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        	else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
                                        		echo "<a class='menuc' href='2020/eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
										}else{
                                        	
											if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]==8541 || $_SESSION["ciiu4"]==8542 || $_SESSION["ciiu4"]==8543 || $_SESSION["ciiu4"]==8544)) {
                                        		echo "<a class='menuc' href='eas_perseduca.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        	else if (isset($_SESSION["ciiu4"]) && ($_SESSION["ciiu4"]!=8541 && $_SESSION["ciiu4"]!=8542 && $_SESSION["ciiu4"]!=8543 && $_SESSION["ciiu4"]!=8544)) {
                                        		echo "<a class='menuc' href='eas_personal.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"poc\"); return false;'>Modulo II <p $ban ></p></a>";
                                        	}
                                        }
										
										if ($periodo >= 2016 && $periodo <= 2017){
											echo "<a class='menuc' href='eas_ingresos2016.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";	
										}elseif($periodo >= 2018 && $periodo != 2020){
											echo "<a class='menuc' href='eas_ingresos2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";	
										}elseif($periodo == 2020 ){
											echo "<a class='menuc' href='2020/eas_ingresos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";	
										}else{ 
											echo "<a class='menuc' href='eas_ingresos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"oco\"); return false;'> Modulo III <p $ban ></p></a>";
										}	
										
										if($periodo >= 2018 && $periodo != 2020){
											echo "<a class='menuc' href='eas_activos2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='eas_unidades2018.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}else if($periodo == 2020){
											echo "<a class='menuc' href='2020/eas_activos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='2020/eas_unidades.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}else{
											echo "<a class='menuc' href='eas_activos.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"acti\"); return false;'>Modulo IV <p $ban ></p></a>";
											echo "<a class='menuc' href='eas_unidades.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"pm\"); return false;'>Modulo V <p $ban ></p></a>";	
										}
										
										switch($periodo){
											case ($periodo<=2011):
												//Se utilza el formulario para TICS antiguo.
												echo "<a class='menuc' href='eas_tics.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
											break;
											case ($periodo==2017):
												//echo "<a class='menuc' href='eas_tics.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI</a>";
												echo "<a class='menuc' href='./eas_tics/ticsn2018.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
											break;
											case ($periodo==2018):
											    echo "<a class='menuc' href='./eas_tics/ticsn2018.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
											    break;
											case ($periodo==2020):
											    echo "<a class='menuc' href='2020/ticsn.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
											    break;
											default:
												//Se utilza el formulario para TICS Nuevo.											
												/*echo "<a class='menuc' href='./eas_tics/ticsn.php" . $qstrestab . "' target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI</a>";*/
											break;
										}
										
										
										/****
										if (substr($_GET['idact'], 0, 3) == '642') {
											echo "<a class='menuc' href='eas_telecomunicaciones.php" . $qstrestab . "target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VII</a>";
										}
										*********/
										
										//Módulo VII - Módulo NIIF
										//@author dmdiazf
										//@since  12/04/2016
										if ($periodo<=2017){
											echo '<a class="menuc" id="mod7" href="./eas_niif.php?idemp=' . $_GET['idemp'] . '&nomest="' . $_GET['nomest'] .'" target="contenido" onMouseOver="muestraOpcion(\'niif\');">Modulo VII <p $ban ></p></a>';	
										}

										if ($periodo==2019){
											echo "<a class='menuc' href='./eas_tics/ticsn2018.php" . $qstrestab . " target='contenido' onMouseOver='muestraOpcion(\"anex\"); return false;'>Modulo VI <p $ban ></p></a>";
										    echo "<a class='menuc' id='modamb 'href='eas_ambiental.php" . $qstrestab . "'  target='contenido' onMouseOver='muestraOpcion(\"ambi\"); return false;'>Modulo VII <p $ban ></p></a>";
										}
										
										if ($periodo==2020){
										    echo "<a class='menuc' id='modamb 'href='2020/eas_ambiental_proteccion.php" . $qstrestab . "'  target='contenido' onMouseOver='muestraOpcion(\"ambi\"); return false;'>Modulo VII <p $ban ></p></a>";
										}
										
										
										echo "<a class='menuc' href='menu.php?atras=si' onMouseOver='muestraOpcion(\"mpp\"); return false;'>Men&uacute; ppal.</a>";
										
										
										if($periodo >= 2018 && $periodo != 2020 ){
											echo "<a class='menuc' href='eas_diagfor2018.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}elseif($periodo == 2020 ){
											echo "<a class='menuc' href='2020/eas_diagfor.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}else{
											echo "<a class='menuc' href='eas_diagfor.php" . $qstrestab . "target='contenido'>Ficha An&aacute;lisis</a>";
										}
										

										echo "<a class='menuf' id='indica2' href='eas_registro.php" . $qstrestab . "' target ='contenido' onMouseOver='muestraOpcion(\"resu\"); return false;'>Paz y Salvo</a>";
										
										echo "</div>";
							   }
							   break;
							   
						/**
						* DMDIAZF - Mayo 07 de 2013
						* CASO PARA EL MODULO DE FUENTE. SE CONTRUYE EL MENU PARA EL MODULO DE FUENTE
						*/
		?>				
		<?php					
						case "FU":	 
							
							$parametros = "idemp=" . $linfu['idnoremp'] . "&nomest=" . $linfu['idnomcom'];
							
							if ($periodo == 2020) {
								/**
								 * Modulo I
								 */
		?>						<a class="<?php echo $linctl["m1"] ? "menucd" : "menuc"; ?>" id="opm1" href="2020/eas_caratula.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('cara'); return false;">Modulo I</a>
		<?php					
								/**
								 * Modulo II
								 */
								 $arrayEducacion = array("8541", "8542", "8543", "8544");
								if( in_array($_SESSION["ciiu4"], $arrayEducacion ) ) {
		?>							<a class="<?php echo $linctl["m2"] ? "menucd" : "menuc"; ?>" id="perso" href="2020/eas_perseduca.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('poc'); return false;">Modulo II</a>
		<?php 					}else{
		?>							<a class="<?php echo $linctl["m2"] ? "menucd" : "menuc"; ?>" id="perso" href="2020/eas_personal.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('poc'); return false;">Modulo II</a>
		<?php 					}
								
								/**
								 * Modulo III
								 */
		?>						<a class="<?php echo $linctl["m3"] ? "menucd" : "menuc"; ?>" id="costo" href="2020/eas_ingresos.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('oco'); return false;">Modulo III</a>
		<?php					
								/**
								 * Modulo IV
								 */
		?>						<a class="<?php echo $linctl["m4"] ? "menucd" : "menuc"; ?>" id="ener" href="2020/eas_activos.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('acti'); return false;">Modulo IV</a>
		<?php					
								/**
								 * Modulo V
								 */
		?>						<a class="<?php echo $linctl["m5"] ? "menucd" : "menuc"; ?>" id="pym" href="2020/eas_unidades.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('pm'); return false;">Modulo V</a>
		<?php					
								/**
								 * Modulo VI
								 */
		?>						<a class="<?php echo $linctl["m6"] ? "menucd" : "menuc"; ?>" id="tics" href="2020/ticsn.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('anex'); return false;">Modulo VI</a>
		<?php					
								/**
								 * Modulo VII
								 */
		?>						<a class="<?php echo $linctl["m7"] ? "menucd" : "menuc"; ?>" id="modamb" href="2020/eas_ambiental_proteccion.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('ambi'); return false;">Modulo VII</a>
		<?php					
							 	/**
								 * Validar formulario
								 */				
								 $visible = "hidden";
								 if( $linctl["m1"] && $linctl["m2"] && $linctl["m3"] && $linctl["m4"] && $linctl["m5"] && $linctl["m6"] && $linctl["m7"] ){
									$controlBuscaSQL = "SELECT estado FROM eas_control WHERE idnoremp = " . $idemp ." AND periodo=" . $periodo ;
                                                                        $controlResult = mysqli_query( $con, $controlBuscaSQL);
                                                                        $dataEstadoControl=mysqli_fetch_array($controlResult);
                                                                        if($dataEstadoControl['estado'] < 3){
                                                                            $controlBuscaSQL = "update eas_control set estado=3 WHERE idnoremp = " . $idemp ." AND periodo=" . $periodo ;
                                                                            $controlResult = mysqli_query($con, $controlBuscaSQL,);
                                                                            
                                                                        }
                                                                     $visible = "visible";
                                                                 }
		?>						 	<a class="menuc" style="visibility: <?php echo $visible; ?>" id="indica" href="2020/eas_chequeaformu.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('resu'); return false;">Verificar Formulario</a>
		<?php					 
								 /**
								  * Paz y Salvo
								  */
								 
								 if( $linctl['estado'] == 5 ){
		?>						 	<a class="menuc" id="indica2" href="2020/eas_registro.php?<?php echo $parametros;?>" target="contenido" onMouseOver="muestraOpcion('resu'); return false;">Paz y Salvo</a>
		<?php					 }	
							}
							break;
						}
		?>				<div class='descrip' id='txtdescrip'></div>
  		
  		
  		<?php $ciiu3=$_SESSION['ciiu3']; ?>
			</div>
			<div style="float: right; text-align: right; font-family: verdana; color: #333366">
				<span style="font-family: arial; font-size: 11px; font-weight: bold">
				<?php	if($_SESSION['tipou']=="FU")
							echo trim($linfu['idnomcom']);
						else
							echo trim($_SESSION['nombreu']);
				?>
				</span><br>
				<a class="liscara" href="eas_cambioclav.php" target="contenido">Cambiar Clave </a> <a class="liscara" href="index.php" target="_top"> Finalizar Sesi&oacute;n</a><br>
				<!--<a class="formato" href="tmpArchivos/SI-EAS2010-MUS-01.pdf" target="_top">ManualUsuario</a><br>-->
				<a class="formato" href="tmpArchivos/PES-EAS-MUS-01.pdf" target="_top">ManualUsuario</a><br>
				
				
<?php /***	
				
 <? if(substr(substr($ciiu3,0,2),0,4)==55){ ?>
  		<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?  }
	elseif(substr(substr($ciiu3,0,2),0,4)==72){ ?>
  		<a class="formato" href="tmpArchivos/LAR-EAS-MDI-11_INFORMATICA.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?  }
    elseif(substr(substr($ciiu3,0,3),0,4)==641){
?>		<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?  }
	elseif(substr(substr($ciiu3,0,3),0,4)==642){
?> 	    <a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?  }
	elseif(substr(substr($ciiu3,0,3),0,4)==851){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	elseif(substr($ciiu3,0,4)==7491){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	elseif(substr($ciiu3,0,4)==7492){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	elseif(substr($ciiu3,0,4)==8050){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	elseif(substr($ciiu3,0,4)==6340){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	elseif(substr($ciiu3,0,4)==7430){
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
	else{
?>  	<a class="formato" href="tmpArchivos/LAR-EAS-MDI-01_GENERAL.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a> 
<?	}
?> 

***/ ?>

<?php  //DMDIAZF - Mayo 29, 2013
       //Descarga de manuales de diligenciamiento - EAS
       //Segun la actividad de la fuente se descarga un manual distinto
       
				
	   //Se bloquean todos los manuales de diligenciamiento.
	   //No se muestra la opci�n, para el periodo 2013.			
				
	   if ($periodo < 2013){ //Bloqueo de manuales de diligenciamiento para el periodo 2013 				

			   if ($ciiu3==6340){  
		        	//Agencias de Viaje
		        	echo '<a class="formato" href="tmpArchivos/2012/viajes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if(($ciiu3==6411)||($ciiu3==6412)){
		        	//Correo
		        	echo '<a class="formato" href="tmpArchivos/2012/correo.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if ($ciiu3==8050){
		        	//Educacion
		        	echo '<a class="formato" href="tmpArchivos/2012/educacion.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if (($ciiu3==5511)||($ciiu3==5512)||($ciiu3==5513)||($ciiu3==5519)||($ciiu3==5521)||($ciiu3==5522)||($ciiu3==5523)||($ciiu3==5524)||($ciiu3==5525)||($ciiu3==5526)||($ciiu3==5529)||($ciiu3==5530)){
		        	//Hoteles
		        	echo '<a class="formato" href="tmpArchivos/2012/hoteles.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if (($ciiu3==7210)||($ciiu3==7220)||($ciiu3==7230)||($ciiu3==7240)||($ciiu3==7250)||($ciiu3==7290)){
		        	//Informatica
		        	echo '<a class="formato" href="tmpArchivos/2012/informatica.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if ($ciiu3==7430){
		        	//Publicidad
		        	echo '<a class="formato" href="tmpArchivos/2012/publicidad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if(($ciiu3==8511)||($ciiu3==8512)||($ciiu3==8513)||($ciiu3==8514)||($ciiu3==8515)||($ciiu3==8519)){
		        	//Salud
		        	echo '<a class="formato" href="tmpArchivos/2012/salud.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if($ciiu3==7492){
		        	//Seguridad
		        	echo '<a class="formato" href="tmpArchivos/2012/seguridad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if(($ciiu3==6421)||($ciiu3==6422)||($ciiu3==6423)||($ciiu3==6424)||($ciiu3==6425)||($ciiu3==6426)){
		        	//Telecomunicaciones
		        	echo '<a class="formato" href="tmpArchivos/2012/telecomunicaciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else if($ciiu3==7491){
		        	//Temporales
		        	echo '<a class="formato" href="tmpArchivos/2012/temporales.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }
		       else{
		        	//General
		        	echo '<a class="formato" href="tmpArchivos/2012/general.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		       }		 
		       
	   }
	   else if ($periodo == 2016){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819);				
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2016/correo.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2016/ediciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2016/educacion.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2016/hoteles.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2016/informatica.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2016/publicidad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2016/restaurantes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2016/salud.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2016/seguridad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2016/telecomunicaciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2016/temporales.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2016/viajes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else{
					echo '<a class="formato" href="tmpArchivos/2016/general.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
		
		}else if ($periodo == 2018){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209,6399);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819, 5820);	
				$general = array(5224,5210,5221,5222,5223,5229,6010,6020,6810,6820,7710,7730,7721,7722,7729,7740,9511,7210,7220,6910,6920,7320,7010,7020,7110,7120,7420,7490,7410,8292,8211,8219,8220,8230,8291,8299,8110,8121,8129,6391,5911,5912,5913,5914,5920,9001,9002,9003,9004,9005,9006,9007,9008,9601,9602,9603,9609,9311,9312,9319,9200,9321,9329);	
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2016/correo.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2016/ediciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2016/educacion.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2016/hoteles.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2016/informatica.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2016/publicidad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2016/restaurantes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2016/salud.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2016/seguridad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2016/telecomunicaciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2016/temporales.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2016/viajes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
				else{
					//echo '<a class="formato" href="tmpArchivos/2016/general.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
					echo '<a class="formato" href="tmpArchivos/PES-EAS-MDI-01-v15.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
				}
		
		}else if ($periodo >= 2019){
				
			echo '<a class="formato" href="tmpArchivos/PES-EAS-MDI-01 v15.pdf" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
		
		}		
	   else{
			   //$ciiu3 = $_SESSION["ciiu4"];	
			   $ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
			   
			   if (($ciiu3==7911)||($ciiu3==7912)||($ciiu3==7990)){
			   	//Agencias de Viaje
			   	echo '<a class="formato" href="tmpArchivos/2015/viajes.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if(($ciiu3==5310)||($ciiu3==5320)){
			   	//Correo
			   	echo '<a class="formato" href="tmpArchivos/2015/correo.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if (($ciiu3==8541)||($ciiu3==8542)||($ciiu3==8543)||($ciiu3==8544)){
			   	//Educacion
			   	echo '<a class="formato" href="tmpArchivos/2015/educacion.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if (($ciiu3==5511)||($ciiu3==5512)||($ciiu3==5530)||($ciiu3==5513)||($ciiu3==5520)||($ciiu3==5514)||($ciiu3==5519)||($ciiu3==5590)||($ciiu3==5611)||($ciiu3==5613)||($ciiu3==5612)||($ciiu3==5621)||($ciiu3==5629)||($ciiu3==5630)){
			   	//Hoteles
			   	echo '<a class="formato" href="tmpArchivos/2015/hoteles.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if (($ciiu3==6202)||($ciiu3==6201)||($ciiu3==6311)||($ciiu3==6312)||($ciiu3==6209)){
			   	//Informatica
			   	echo '<a class="formato" href="tmpArchivos/2015/informatica.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if ($ciiu3==7310){
			   	//Publicidad
			   	echo '<a class="formato" href="tmpArchivos/2015/publicidad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if(($ciiu3==8610)||($ciiu3==8621)||($ciiu3==8622)||($ciiu3==8691)||($ciiu3==8692)||($ciiu3==8699)||($ciiu3==8710)||($ciiu3==8720)||($ciiu3==8730)){
			   	//Salud
			   	echo '<a class="formato" href="tmpArchivos/2015/salud.docx" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if(($ciiu3==8010)||($ciiu3==8020)||($ciiu3==8030)){
			   	//Seguridad
			   	echo '<a class="formato" href="tmpArchivos/2015/seguridad.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if(($ciiu3==6110)||($ciiu3==6120)||($ciiu3==6130)||($ciiu3==6190)){
			   	//Telecomunicaciones
			   	echo '<a class="formato" href="tmpArchivos/2015/telecomunicaciones.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else if(($ciiu3==7810)||($ciiu3==7820)||($ciiu3==7830)){
			   	//Temporales
			   	echo '<a class="formato" href="tmpArchivos/2015/temporales.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
			   else{
			   	//General
			   	echo '<a class="formato" href="tmpArchivos/2015/general.doc" target="_blank" title="Descargar Manual de Diligenciamiento">MDiligenciamiento</a>';
			   }
							
	   }     
	   	
?>

<?php   //DMDIAZF - Mayo 28, 2013
		//Descarga de formularios borrador - EAS
        //Segun la actividad de la fuente se descarga un formulario distinto
        
		if ($periodo<=2013){

		        if ($ciiu3==6340){  
		        	//Agencias de Viaje
		        	echo '<a class="formato" href="tmpArchivos/2012/viajes.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if(($ciiu3==6411)||($ciiu3==6412)){
		        	//Correo
		        	echo '<a class="formato" href="tmpArchivos/2012/correo.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if ($ciiu3==8050){
		        	//Educacion
		        	echo '<a class="formato" href="tmpArchivos/2012/educacion.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if (($ciiu3==5511)||($ciiu3==5512)||($ciiu3==5513)||($ciiu3==5519)||($ciiu3==5521)||($ciiu3==5522)||($ciiu3==5523)||($ciiu3==5524)||($ciiu3==5525)||($ciiu3==5526)||($ciiu3==5529)||($ciiu3==5530)){
		        	//Hoteles
		        	echo '<a class="formato" href="tmpArchivos/2012/hoteles.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if (($ciiu3==7210)||($ciiu3==7220)||($ciiu3==7230)||($ciiu3==7240)||($ciiu3==7250)||($ciiu3==7290)){
		        	//Informatica
		        	echo '<a class="formato" href="tmpArchivos/2012/informatica.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if ($ciiu3==7430){
		        	//Publicidad
		        	echo '<a class="formato" href="tmpArchivos/2012/publicidad.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if(($ciiu3==8511)||($ciiu3==8512)||($ciiu3==8513)||($ciiu3==8514)||($ciiu3==8515)||($ciiu3==8519)){
		        	//Salud
		        	echo '<a class="formato" href="tmpArchivos/2012/salud.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if($ciiu3==7492){
		        	//Seguridad
		        	echo '<a class="formato" href="tmpArchivos/2012/seguridad.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if(($ciiu3==6421)||($ciiu3==6422)||($ciiu3==6423)||($ciiu3==6424)||($ciiu3==6425)||($ciiu3==6426)){
		        	//Telecomunicaciones
		        	echo '<a class="formato" href="tmpArchivos/2012/telecomunicaciones.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else if($ciiu3==7491){
		        	//Temporales
		        	echo '<a class="formato" href="tmpArchivos/2012/temporales.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        else{
		        	//General
		        	echo '<a class="formato" href="tmpArchivos/2012/general.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
		        }
		        
		}
		else if ($periodo == 2016){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819);				
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2016/correo.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2016/ediciones.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';		
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2016/educacion.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2016/hoteles.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2016/informatica.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2016/publicidad.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2016/restaurantes.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2016/salud.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2016/seguridad.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2016/telecomunicaciones.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2016/temporales.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2016/viajes.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else{
					echo '<a class="formato" href="tmpArchivos/2016/general.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
		
		}else if ($periodo == 2018){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209,6399);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819, 5820);	
				$general = array(5224,5210,5221,5222,5223,5229,6010,6020,6810,6820,7710,7730,7721,7722,7729,7740,9511,7210,7220,6910,6920,7320,7010,7020,7110,7120,7420,7490,7410,8292,8211,8219,8220,8230,8291,8299,8110,8121,8129,6391,5911,5912,5913,5914,5920,9001,9002,9003,9004,9005,9006,9007,9008,9601,9602,9603,9609,9311,9312,9319,9200,9321,9329);	
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO CORREO.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO EDICIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO EDUCACION.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO HOTELES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO INFORMATICA.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO PUBLICIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO RESTAURANTES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO SALUD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO SEGURIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO TELECOMUNICACIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO SUMINISTRO PER.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO AGENCIAS DE  VIAJE.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $general)){
					echo '<a class="formato" href="tmpArchivos/2018/FORMULARIO GENERAL.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
		
		}
		else if ($periodo >= 2019 && $periodo != 2020 ){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209,6399);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819, 5820);	
				$general = array(5224,5210,5221,5222,5223,5229,6010,6020,6810,6820,7710,7730,7721,7722,7729,7740,9511,7210,7220,6910,6920,7320,7010,7020,7110,7120,7420,7490,7410,8292,8211,8219,8220,8230,8291,8299,8110,8121,8129,6391,5911,5912,5913,5914,5920,9001,9002,9003,9004,9005,9006,9007,9008,9601,9602,9603,9609,9311,9312,9319,9200,9321,9329);	
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_CORREO.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_EDICIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_EDUCACION.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_HOTELES_p.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_INFORMATICA.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_PUBLICIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_ RESTAURANTES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_ SALUD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_ SEGURIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}  
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_TELECOMUNICACIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_SUMINISTRO_ PER.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_AGENCIAS_DE_VIAJE.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $general)){
					echo '<a class="formato" href="tmpArchivos/2019/FORMULARIO_GENERAL.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
		}else if ($periodo == 2020 ){
				
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				$viajes = array(7911,7912,7990);
				$correo = array(5310,5320);
				$educacion = array(8541,8542,8543,8544);
				$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);				                
				$informatica = array(6202,6201,6311,6312,6209,6399);
				$publicidad = array(7310);
				$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
				$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
				$seguridad = array(8010,8020,8030);
				$telecomunicaciones = array(6110,6120,6130,6190);
				$temporales = array(7810,7820,7830);				
				$ediciones = array(5811, 5812, 5813, 5819, 5820);	
				$general = array(5224,5210,5221,5222,5223,5229,6010,6020,6810,6820,7710,7730,7721,7722,7729,7740,9511,7210,7220,6910,6920,7320,7010,7020,7110,7120,7420,7490,7410,8292,8211,8219,8220,8230,8291,8299,8110,8121,8129,6391,5911,5912,5913,5914,5920,9001,9002,9003,9004,9005,9006,9007,9008,9601,9602,9603,9609,9311,9312,9319,9200,9321,9329);	
				
				if (in_array($ciiu3, $correo)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_CORREO.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $ediciones)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_EDICIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $educacion)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_EDUCACION.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $hoteles)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_HOTELES_p.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $informatica)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_INFORMATICA.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $publicidad)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_PUBLICIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $restaurantes)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_ RESTAURANTES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}				
				else if (in_array($ciiu3, $salud)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_ SALUD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $seguridad)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_ SEGURIDAD.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}  
				else if (in_array($ciiu3, $telecomunicaciones)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_TELECOMUNICACIONES.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $temporales)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_SUMINISTRO_ PER.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';	
				}
				else if (in_array($ciiu3, $viajes)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_AGENCIAS_DE_VIAJE.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (in_array($ciiu3, $general)){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_GENERAL.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if ($ciiu3 == "9102"){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_CLASE_9102.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if ($ciiu3 == "9493"){
					echo '<a class="formato" href="tmpArchivos/2020/FORMULARIO_CLASE_9493.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
												
		}else if ($periodo == 2017){
		
			$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
		
			$viajes = array(7911,7912,7990);
			$correo = array(5310,5320);
			$educacion = array(8541,8542,8543,8544);
			$hoteles = array(5511,5512,5530,5513,5520,5514,5519,5590);
			$informatica = array(6202,6201,6311,6312,6209);
			$publicidad = array(7310);
			$restaurantes = array(5611, 5612, 5613, 5619, 5621, 5629, 5630);
			$salud = array(8610,8621,8622,8691,8692,8699,8710,8720,8730);
			$seguridad = array(8010,8020,8030);
			$telecomunicaciones = array(6110,6120,6130,6190);
			$temporales = array(7810,7820,7830);
			$ediciones = array(5811, 5812, 5813, 5819);
		
			if (in_array($ciiu3, $correo)){
				echo '<a class="formato" href="tmpArchivos/2017/correo.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $ediciones)){
				echo '<a class="formato" href="tmpArchivos/2017/ediciones.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $educacion)){
				echo '<a class="formato" href="tmpArchivos/2017/educacion.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $hoteles)){
				echo '<a class="formato" href="tmpArchivos/2017/hoteles.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $informatica)){
				echo '<a class="formato" href="tmpArchivos/2017/informatica.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $publicidad)){
				echo '<a class="formato" href="tmpArchivos/2017/publicidad.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $restaurantes)){
				echo '<a class="formato" href="tmpArchivos/2017/restaurantes.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $salud)){
				echo '<a class="formato" href="tmpArchivos/2017/salud.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $seguridad)){
				echo '<a class="formato" href="tmpArchivos/2017/seguridad.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $telecomunicaciones)){
				echo '<a class="formato" href="tmpArchivos/2017/telecomunicaciones.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $temporales)){
				echo '<a class="formato" href="tmpArchivos/2017/temporales.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else if (in_array($ciiu3, $viajes)){
				echo '<a class="formato" href="tmpArchivos/2017/viajes.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
			else{
				echo '<a class="formato" href="tmpArchivos/2017/general.xls" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
			}
		
		}
		else{
				$ciiu3 = (isset($_SESSION["ciiu4"]))?$_SESSION["ciiu4"]:$_SESSION["ciiu3"];
				
				
				if (($ciiu3==7911)||($ciiu3==7912)||($ciiu3==7990)){
					//Agencias de Viaje
					echo '<a class="formato" href="tmpArchivos/2012/viajes.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if(($ciiu3==5310)||($ciiu3==5320)){
					//Correo
					echo '<a class="formato" href="tmpArchivos/2012/correo.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (($ciiu3==8541)||($ciiu3==8542)||($ciiu3==8543)||($ciiu3==8544)){
					//Educacion
					echo '<a class="formato" href="tmpArchivos/2012/educacion.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (($ciiu3==5511)||($ciiu3==5512)||($ciiu3==5530)||($ciiu3==5513)||($ciiu3==5520)||($ciiu3==5514)||($ciiu3==5519)||($ciiu3==5590)||($ciiu3==5611)||($ciiu3==5613)||($ciiu3==5612)||($ciiu3==5621)||($ciiu3==5629)||($ciiu3==5630)){
					//Hoteles
					echo '<a class="formato" href="tmpArchivos/2012/hoteles.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if (($ciiu3==6202)||($ciiu3==6201)||($ciiu3==6311)||($ciiu3==6312)||($ciiu3==6209)){
					//Informatica
					echo '<a class="formato" href="tmpArchivos/2012/informatica.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if ($ciiu3==7310){
					//Publicidad
					echo '<a class="formato" href="tmpArchivos/2012/publicidad.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if(($ciiu3==8610)||($ciiu3==8621)||($ciiu3==8622)||($ciiu3==8691)||($ciiu3==8692)||($ciiu3==8699)||($ciiu3==8710)||($ciiu3==8720)||($ciiu3==8730)){
					//Salud
					echo '<a class="formato" href="tmpArchivos/2012/salud.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if(($ciiu3==8010)||($ciiu3==8020)||($ciiu3==8030)){
					//Seguridad
					echo '<a class="formato" href="tmpArchivos/2012/seguridad.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if(($ciiu3==6110)||($ciiu3==6120)||($ciiu3==6130)||($ciiu3==6190)){
					//Telecomunicaciones
					echo '<a class="formato" href="tmpArchivos/2012/telecomunicaciones.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else if(($ciiu3==7810)||($ciiu3==7820)||($ciiu3==7830)){
					//Temporales
					echo '<a class="formato" href="tmpArchivos/2012/temporales.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				else{
					//General
					echo '<a class="formato" href="tmpArchivos/2012/general.xlsx" target="_blank" title="Descargar formato en blanco para borrador [PDF]">Formulario Borrador</a>';
				}
				
		}		        
?>


<?php   if (isset($_GET['nomest'])) {
			$qstrestab = "?idemp=" . $_GET['idemp'] . "&nomest=" . $_GET['nomest'];
			echo "<a class='formato' href='frmimpre.php" . $qstrestab . "' target='_blank'>Formulario Diligenciado</a>";
			echo "&nbsp";
			if ($_SESSION['tipou'] == "FU") {
				if ($numest == $numeroest) {
					echo "<a style='font-family: arial; font-size: 10px; font-weight: bold' href='registro.php?idemp=" . $restctl['idnoremp'] . "' target='_blank'>Registro industrial</a>";
				}
			}
	   }
?>
</div>
</body>
</html>
