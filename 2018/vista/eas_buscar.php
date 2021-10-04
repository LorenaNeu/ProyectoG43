<?php
	if (session_id() == "") {
		session_start();
	}
	$existe = "existe";
	$mostrar = "";
	include '../../servicio/eas_conecta.php';
	$region = $_SESSION['region'];
	$tipo_usu = $_SESSION['tipou'];
    $periodo= $_SESSION['anoproc'];
    
    if ($periodo > 2012) {
    	$campociiu = "ciiu4 AS idact";
    }
    else {
    	$campociiu = "idact";
    }
	
    
	if (isset($_POST['envbus'])) {
		$mostrar = "SI";
		if ($_POST['buscar'] == "nu") {
			if ($region == 99) {
				$consulta = "SELECT DISTINCT a.idnoremp, a.idnomcom, CASE b.estado WHEN 0 THEN 'Pendiente' WHEN 1 THEN 'Distribuido'
					WHEN 2 THEN 'En Digitaci&oacute;n' WHEN 3 THEN 'Digitado' WHEN 4 THEN 'En Revisi&oacute;n' WHEN 5 THEN 'Revisado'
					END AS letraestado, b.prio2, a." . $campociiu . ", b.periodo FROM eas_directorio a, eas_control b
					WHERE a.idnoremp=" . trim($_POST['cadenab']) . " AND b.periodo= ".$periodo." AND
					b.idnoremp = a.idnoremp  AND a.periodo = b.periodo  ORDER BY a.idnoremp";
			}
			else {
				$consulta = "SELECT DISTINCT a.idnoremp, a.idnomcom, CASE b.estado WHEN 0 THEN 'Pendiente' WHEN 1 THEN 'Distribuido'
					WHEN 2 THEN 'En Digitaci&oacute;n' WHEN 3 THEN 'Digitado' WHEN 4 THEN 'En Revisi&oacute;n' WHEN 5 THEN 'Revisado'
					END AS letraestado, b.prio2, a." . $campociiu . ", b.periodo FROM eas_directorio a, eas_control b
					WHERE a.idnoremp=" . trim($_POST['cadenab']) . " AND a.csede=" . $region . " AND b.idnoremp = a.idnoremp 
					AND b.periodo= ".$periodo."  AND a.periodo = b.periodo ORDER BY a.idnoremp";
			}
		}
		else {
			
			if ($region == 99) {
				$consulta = "SELECT DISTINCT a.idnoremp, a.idnomcom, CASE b.estado WHEN 0 THEN 'Pendiente' WHEN 1 THEN 'Distribuido'
					WHEN 2 THEN 'En Digitaci&oacute;n' WHEN 3 THEN 'Digitado' WHEN 4 THEN 'En Revisi&oacute;n' WHEN 5 THEN 'Revisado'
					END AS letraestado, b.prio2, a." . $campociiu . ", b.periodo FROM eas_directorio a, eas_control b 
					WHERE LOCATE('" . trim($_POST['cadenab']) . "', a.idnomcom) AND b.idnoremp = a.idnoremp AND 
					b.periodo= ".$periodo."  AND a.periodo = b.periodo  ORDER BY a.idnoremp";
			}
			else {
				$consulta = "SELECT DISTINCT a.idnoremp, a.idnomcom, CASE b.estado WHEN 0 THEN 'Pendiente' WHEN 1 THEN 'Distribuido'
					WHEN 2 THEN 'En Digitaci&oacute;n' WHEN 3 THEN 'Digitado' WHEN 4 THEN 'En Revisi&oacute;n' WHEN 5 THEN 'Revisado'
					END AS letraestado, b.prio2, a." . $campociiu . ", b.periodo FROM eas_directorio a, eas_control b 
					WHERE LOCATE('" . trim($_POST['cadenab']) . "', a.idnomcom) AND a.csede=" . $region . 
					" AND b.idnoremp = a.idnoremp AND b.periodo= ".$periodo."  AND a.periodo = b.periodo ORDER BY a.idnoremp";
			}
			
		}
                //echo $consulta;
		$resultado = mysqli_query($con, $consulta );
		$numero_est = mysqli_num_rows($resultado);
		if ($numero_est == 0) {
			$existe = "NO";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Encuesta Anual de Servicios - Formulario Electr?nico</title>
		<link rel = stylesheet href = "../../css/formelec.css" type="text/css">
		<script type="text/javascript" src="../js/buscara.js"></script>
	</head>
	<body>
		<form name="frmbusca" style="position: relative; left: 10%; top: 0.1%; margin-bottom: 0px; width: 80%" method="POST" onSubmit="return buscaFormu();">
			<fieldset class="marcos">
				<legend>Buscar por:</legend>
				<input type="radio" name="buscar" value="nu" checked /><span class="labela">No Orden</span>
				<input type="radio" name="buscar" value="no" /><span class="labela">Nombre</span>
				<input type="text" id="txtb" name="cadenab" style="width: 41.6%; font-family: verdana; font-size: 9px" />
				<input type="submit" id="btnb" name="envbus" value="Buscar" style="font-family: verdana; font-size: 9px" />
				<?php
					if ($region == 99 AND $tipo_usu == "CR") {
						echo "<a href='eas_listacap.php' target='contenido' style='position: absolute; right: 2px'>Asignados</a>";
					}
				?>
			</fieldset>
		</form>
		<?php
			if ($existe == "NO") {
				echo "<div id='respuesta' style='position: absolute; left: 10%; top: 50px; font-family: verdana; font-size: 12px; color: #FF0000'>";
				echo "EMPRESA no existe o no corresponde a regional";
				echo "</div>";
			}
			else {
				if ($mostrar == "SI") {
					$colorcell = "#F5F5F5";
					echo "<div id='lista' style='width: 80%; height: 400px; border: 1px solid #CCFFCC; position: absolute; left: 10%; top: 50px; overflow: auto'>";
					echo "<table cellspacing='1' style='width: 98%'>";
					while ($linest = mysqli_fetch_array($resultado)) {
						if ($colorcell == "#FFFFFF") {
							$colorcell = "#F5F5F5";
						}
						else {
							$colorcell = "#FFFFFF";
						}
						echo "<tr>";
						echo "<td style='font-family: arial; font-size:10px; text-align: right; background-color: " . $colorcell . "'>" . $linest['idnoremp'] . "</td>";
						echo "<td style = 'background-color: " . $colorcell . "'><a class='liscara' href='menu.php?idemp=" . $linest['idnoremp'] . "&nomest=" . $linest['idnomcom'] . "&idact=" . $linest['idact'] . "' target='cabeza'>" . $linest['idnomcom'] . "</a></td>";
						echo "<td style='font-family: arial; font-size:10px; background-color: " . $colorcell . "'>" . $linest['letraestado'] . "</td>";
						echo "<td style='font-family: arial; font-size:10px; background-color: " . $colorcell . "'>" . $linest['prio2'] . "</td>";
						echo "<td style='font-family: arial; font-size:10px; background-color: " . $colorcell . "'>" . $linest['periodo'] . "</td>";
						echo "</tr>";
					}
					echo "</table>";
					echo "</div>";
				}
			}
		?>
	</body>
</html>
