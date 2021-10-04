<?php
if (session_id() == "") {
	session_start();
}
 
include './servicio/eas_conecta.php';

$periproc = "SELECT * FROM eas_regcontrol ORDER BY periodoproc DESC";
$resperi = mysqli_query($con, $periproc);
//$lperiodo = mysql_fetch_array($resperi);
$lperiodo = mysqli_fetch_array($resperi, MYSQLI_ASSOC);
$anoproc = $lperiodo['periodoproc'];

$mensaje = "";
if (isset($_POST['submit'])) {
	$idusuario = $_POST['idusu'];
	$passw = $_POST['pwd'];
	$consulta = "SELECT * FROM eas_usuarios WHERE ident LIKE BINARY '$idusuario' AND clave LIKE BINARY '$passw' AND estado = 1";
	//$consulta = "SELECT * FROM eas_usuarios WHERE ident LIKE BINARY '$idusuario' AND estado = 1";
	//echo $consulta;
	$resultado = mysqli_query($con, $consulta);
	$numregs = mysqli_num_rows($resultado);

	if ($numregs == 0) {
		$mensaje = "Identificaci&oacute;n/Clave Incorrectos";
	} else {
		$row = mysqli_fetch_array($resultado);
		$_SESSION['nombreu'] = $row['nombre'];
		$_SESSION['tipou'] = $row['tipo'];
		$_SESSION['idusu'] = $row['ident'];
		$_SESSION['numero'] = $row['numemp'];

		$_SESSION['region'] = $row['region'];

		$_SESSION['anoproc'] = $anoproc;
		$numemp = $row['numemp'];
		$sql = "SELECT * FROM eas_directorio WHERE idnoremp  = $numemp ";
		$res_ciiu = mysqli_query($sql, $con);
		$row_ciiu = mysqli_fetch_array($res_ciiu);
		$_SESSION['ciiu3'] = $row_ciiu['idact'];

		if (isset($_GET['debug-dane'])) {
			$_SESSION['debug-dane'] = "SI";
		}else{
			unset( $_SESSION['debug-dane'] );
		}

		header("location: eas_menu.htm");
		//			header("location: fds.htm");

	}
}else{
    unset( $_SESSION );
}

$mantenimiento = false;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>DANE - Encuesta Anual de Servicios - Formulario Electr&oacute;nico</title>
        <link rel="shortcut icon" href="images/favicon.ico">
		<link rel = stylesheet href = "css/formelec.css" type="text/css">
	</head>
<body onLoad="document.forms[0].elements[0].focus()">
		
<div id="imageHeader" style="text-align: center">			
	<div class="cabeza" style="margin:0 auto 0 auto; width:1140px; height: 100px;">
		<img src="images/newlogoeas.png">		
	</div>
</div>	
		
<div class="introtop">Formulario Electr&oacute;nico Encuesta Anual de Servicios - EAS - <?php echo $anoproc; ?></div>
		
<div class="intro"> Se&ntilde;or Empresario:<br />
  <br />
  El Departamento Administrativo Nacional de Estad&iacute;stica <b>DANE</b>, en el marco
  de su plan de modernizaci&oacute;n de los instrumentos de recolecci&oacute;n de las encuestas
  econ&oacute;micas y con el prop&oacute;sito de agilizar y facilitar el reporte correcto y
  oportuno de los datos estad&iacute;sticos requeridos por la <b>ENCUESTA ANUAL DE SERVICIOS</b>,
  pone a su disposici&oacute;n el presente formulario electr&oacute;nico, con el cual podr&aacute;
  diligenciar y verificar en l&iacute;nea la consistencia de su informaci&oacute;n.
  <br />
  <br />
  El objetivo de la EAS es conocer la estructura y la evoluci&oacute;n del sector servicios a nivel nacional 
  permitiendo el an&aacute;lisis y la conformaci&oacute;n de agregados econ&oacute;micos de las actividades objeto de estudio
  <br />
  <br />
  Un funcionario de nuestra entidad, estar&aacute; en todo momento atento para prestarle
  la asesor&iacute;a y orientaci&oacute;n necesaria. <br />
</div>
<div class="login" id="divpass"> 
<?php if( $mantenimiento ){ ?>
		<img src="images/mantenimiento.png" />
<?php 
		exit;
	}  
?>	
  <form id="login" method="POST" action="index.php<?php echo ( isset($_GET['debug-dane']) ? "?debug-dane" : ""); ?>">
    Usuario: 
    <input class="entra2" type="text" name="idusu">
    Clave: 
    <input class="entra2" type="password" name="pwd">
    <input type="submit" name="submit" value="Entrar"></td>
    
  </form>
</div>



<div class="mensa"><?php echo $mensaje?></div>
		<div class="piepag">
			DANE: Carrera 59 Nro 26-70 Interior 1 CAN, Conmutador (571)5978300 - Fax (571)5978399 <br>
			Bogot&aacute; D.C., Colombia - Sur Am&eacute;rica
		</div>
	</body>
</html>
