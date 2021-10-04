<?php 
	
	class GeneraEnergia {
	
		var $cnx = null;
	
		function __construct() {
			require("../../servicio/eas_conecta.php");
			$this->cnx = $con;
		}
	
		/**
		 * Funcion para obtener el formulario de Generacion de Energía Periodo 2016
		 * @author Daniel M. Díaz
		 * @since  2017/02/28
		 **/
		public function generacionEnergiaFORM($periodo, $idnoremp){
			require("../../servicio/eas_conecta.php");				
			$data = $this->obtenerDatosEnergia($this->cnx, $periodo, $idnoremp);					
			$this->dibujarFormulario($data,$periodo);
		}
	
	
		/**
		 * Funcion para obtener los datos de generacion del formulario de energia periodo 2016
		 * @author Daniel M. Díaz
		 * @since  2017/02/28
		 **/
		private function obtenerDatosEnergia($cnx, $periodo, $idnoremp){
			$data = array();
			$sql = "SELECT * FROM eas_genenergia WHERE periodo = $periodo AND idnoremp = $idnoremp";			
			$res = mysqli_query($cnx, $sql);				
			if (mysqli_num_rows($res) > 0){
				while ($row = mysqli_fetch_array($res)){
					$data["periodo"] = $row["periodo"];
					$data["idnoremp"] = $row["idnoremp"];				
					$data["nplante"] = $row["nplante"];										
					$data["ckwpe"] = $row["ckwpe"];									
					$data["ttplane"] = $row["ttplane"];	
					$data["cplanteg"] = $row["cplanteg"];
					$data["cplanted"] = $row["cplanted"];
					$data["cplanteo"] = $row["cplanteo"];				
					$data["nplantr"] = $row["nplantr"];
					$data["ckwpr"] = $row["ckwpr"];
					$data["ttplanr"] = $row["ttplanr"];			
					$data["cplantrg"] = $row["cplantrg"];						
					$data["cplantrd"] = $row["cplantrd"];						
					$data["cplantro"] = $row["cplantro"];
					$data["nplants"] = $row["nplants"];										
					$data["ckwps"] = $row["ckwps"];									
					$data["ttplans"] = $row["ttplans"];
					$data["nplanteo"] = $row["nplanteo"];
					$data["ckwpeo"] = $row["ckwpeo"];
					$data["ttplaneo"] = $row["ttplaneo"];
					$data["nplanto"] = $row["nplanto"];
					$data["ckwpo"] = $row["ckwpo"];
					$data["ttplano"] = $row["ttplano"];	
					$data["emcual"] = $row["emcual"];
					$data["rescual"] = $row["rescual"];
					$data["otrcual"] = $row["otrcual"];					
				}
			}
			else{
				$data["periodo"] = "";
				$data["idnoremp"] = "";				
				$data["nplante"] = "";										
				$data["ckwpe"] = "";									
				$data["ttplane"] = "";	
				$data["cplanteg"] = "";
				$data["cplanted"] = "";
				$data["cplanteo"] = "";				
				$data["nplantr"] = "";
				$data["ckwpr"] = "";
				$data["ttplanr"] = "";			
				$data["cplantrg"] = "";						
				$data["cplantrd"] = "";						
				$data["cplantro"] = "";
				$data["nplants"] = "";										
				$data["ckwps"] = "";									
				$data["ttplans"] = "";
				$data["nplanteo"] = "";
				$data["ckwpeo"] = "";
				$data["ttplaneo"] = "";
				$data["nplanto"] = "";
				$data["ckwpo"] = "";
				$data["ttplano"] = "";	
				$data["emcual"] = "";
				$data["rescual"] = "";
				$data["otrcual"] = "";
			}			
			mysqli_free_result($res);		
			return $data;				
		}
	
	
	
		/**
		 * Dibuja el formulario para la generación de energía Periodo 2016
		 * @author Daniel M. Díaz
		 * @since  2017/02/28
		 **/
		private function dibujarFormulario($data,$periodo){	
			  echo '<fieldset class="marcos">
					<legend class="leyenda">Generaci&oacute;n de Energ&iacute;a por Parte de la Empresa</legend>
					<div style="float:left; font-family: arial, font-size: 9px; margin-right: 3px; text-align: right; padding-top: 4px;">Relacione las caracter&iacute;sticas y el uso de las plantas utilizadas por la empresa para el suministro de energ&iacute;a el&eacute;ctrica en el pa&iacute;s en caso interrupciones, durante el a&ntilde;o: '.$periodo.'</div>
					<br/><br/>					
					<table width="100%" style="border: 2px solid #00008b; border-collapse:collapse; border: none;" border="1">
					<thead style="background-color: #00008b; color: #fff; border: 1px solid #00008b;">
					<tr>
						<td align="center">Item</td>
						<td width="10%" align="center">Tipo de Planta<br/>(1)</td>
						<td width="10%" align="center">N&uacute;mero de Plantas<br/>(2)</td>
						<td width="10%" align="center">Capacidad total - kW<br/>(3)</td>
						<td width="10%" align="center">Tiempo total de utilizaci&oacute;n (horas/a&ntilde;o)<br/>(4)</td>
						<td align="center" colspan="3">Cantidad (Galones/a&ntilde;o) de combustible con el que funciona la planta<br/>(5)</td>								
					</tr>
					</thead>
					<tbody>
					<tr style="border-bottom: 1px solid #00008b;">
						<td rowspan="3" align="center">1</td>
						<td rowspan="3" style="padding-left: 10px;">Emergencia</td>
						<td rowspan="3" style="text-align: center"><input type="text" id="NPLANTE" name="NPLANTE" value="'.$data["nplante"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td rowspan="3" style="text-align: center"><input type="text" id="CKWPE" name="CKWPE" value="'.$data["ckwpe"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td rowspan="3" style="text-align: center"><input type="text" id="TTPLANE" name="TTPLANE" value="'.$data["ttplane"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>';
					
					if ($data["cplanteg"] > 0)						
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme1" name="radEme" value="1" checked="checked"/>Gasolina pura o mezclada con etanol (Galones)</td>';
					else
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme1" name="radEme" value="1"/>Gasolina pura o mezclada con etanol (Galones)</td>';

					
					echo '<td style="text-align: center"><input type="text" id="CPLANTEG" name="CPLANTEG" value="'.$data["cplanteg"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						
					</tr>
					<tr>';
					
					if ($data["cplanted"] > 0)
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme2" name="radEme" value="2" checked="checked"/>Diesel - Biodiesel-ACPM</td>';
					else	
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme2" name="radEme" value="2"/>Diesel - Biodiesel-ACPM</td>';
						
					echo '<td style="text-align: center"><input type="text" id="CPLANTED" name="CPLANTED" value="'.$data["cplanted"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
					      
					</tr>
					<tr>'; 

					if ($data["cplanteo"] > 0)	
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme3" name="radEme" value="3" checked="checked"/>Gas (m3)</td>'; 
					else 	
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radEme3" name="radEme" value="3"/>Gas (m3)</td>'; 
						
					echo '<td style="text-align: center"><input type="text" id="CPLANTEO" name="CPLANTEO" value="'.$data["cplanteo"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<!--<td>Cu&aacute;l ?:&nbsp;<input type="text" id="EMCUAL" name="EMCUAL" value="'.$data["emcual"].'" style="width: 90%; font-family: arial; font-size: 9px;"/></td>-->
					</tr>
					<tr>
						<td rowspan="3" align="center">2</td>
						<td rowspan="3" style="padding-left: 10px;">Respaldo</td>
						<td rowspan="3" style="text-align: center"><input type="text" id="NPLANTR" name="NPLANTR" value="'.$data["nplantr"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td rowspan="3" style="text-align: center"><input type="text" id="CKWPR" name="CKWPR" value="'.$data["ckwpr"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td rowspan="3" style="text-align: center"><input type="text" id="TTPLANR" name="TTPLANR" value="'.$data["ttplanr"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>';
					
					if ($data["cplantrg"] > 0)		
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp1" name="radResp" value="1" checked="checked"/>Gasolina pura o mezclada con etanol (Galones)</td>';
					else
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp1" name="radResp" value="1"/>Gasolina pura o mezclada con etanol (Galones)</td>';
						
					echo '<td style="text-align: center"><input type="text" id="CPLANTRG" name="CPLANTRG" value="'.$data["cplantrg"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						
					</tr>
					<tr>';
					
					if ($data["cplantrd"] > 0)
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp2" name="radResp" value="2" checked="checked"/>Diesel - Biodiesel-ACPM </td>';
					else	
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp2" name="radResp" value="2"/>Diesel - Biodiesel-ACPM </td>';
						
					echo '<td style="text-align: center"><input type="text" id="CPLANTRD" name="CPLANTRD" value="'.$data["cplantrd"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						
					</tr>
					<tr>'; 

					if ($data["cplantro"] > 0)	
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp3" name="radResp" value="3" checked="checked"/>Gas (m3)</td>'; 
					else
						echo '<td style="padding-left: 10px;"><input type="checkbox" id="radResp3" name="radResp" value="3"/>Gas (m3)</td>'; 
						
					echo '<td style="text-align: center"><input type="text" id="CPLANTRO" name="CPLANTRO" value="'.$data["cplantro"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<!--<td>Cu&aacute;l ?:&nbsp;<input type="text" id="RESCUAL" name="RESCUAL" value="'.$data["rescual"].'" style="width: 90%; font-family: arial; font-size: 9px"/></td>-->
					</tr>
					<tr>
						<td align="center">3</td>
						<td style="padding-left: 10px;">Solar</td>
						<td style="text-align: center"><input type="text" id="NPLANTS" name="NPLANTS" value="'.$data["nplants"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td style="text-align: center"><input type="text" id="CKWPS" name="CKWPS" value="'.$data["ckwps"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>								
						<td style="text-align: center"><input type="text" id="TTPLANS" name="TTPLANS" value="'.$data["ttplans"].'" style="width: 80%; font-family: arial; font-size: 9px"/></td>
						<td colspan="3" rowspan="2" align="center">En caso de tener dos o m&aacute;s plantas en un mismo tipo, sume la capacidad de las plantas de ese tipo. Ejemplo: sume todas las plantas de emergencia con que cont&oacute; la empresa en ese a&ntilde;o</td>
					</tr>
					<tr>
						<td align="center">4</td>
						<td style="padding-left: 10px;">E&oacute;lica</td>
						<td style="text-align: center"><input type="text" id="NPLANTEO" name="NPLANTEO" value="'.$data["nplanteo"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>
						<td style="text-align: center"><input type="text" id="CKWPEO" name="CKWPEO" value="'.$data["ckwpeo"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>								
						<td style="text-align: center"><input type="text" id="TTPLANEO" name="TTPLANEO" value="'.$data["ttplaneo"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>
					</tr>
					<tr>
						<td align="center">5</td>
						<td style="padding-left: 10px;">Otro tipo de planta</td>
						<td style="text-align: center"><input type="text" id="NPLANTO" name="NPLANTO" value="'.$data["nplanto"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>
						<td style="text-align: center"><input type="text" id="CKWPO" name="CKWPO" value="'.$data["ckwpo"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>								
						<td style="text-align: center"><input type="text" id="TTPLANO" name="TTPLANO" value="'.$data["ttplano"].'" style="width: 80%; font-family: arial; font-size: 9px;"/></td>
						<td colspan="3">&iquest; Cu&aacute;l tipo de planta ?&nbsp;&nbsp;<input type="text" id="OTRCUAL" name="OTRCUAL" value="'.$data["otrcual"].'" style="width: 81%; font-family: arial; font-size: 9px;"/></td>
					</tr>
					</tbody>
					</table>						
					<br/>
					<div style="font-weight: normal;"><span style="color: red; font-weight: bolder;">Equipo suministro el&eacute;ctrico de emergencia:&nbsp;</span>Es aquel que suministra energ&iacute;a el&eacute;ctrica en un establecimiento al momento de sufrir cortes inesperados de electricidad. &Eacute;sta se suministra por un n&uacute;mero de horas limitado (entre 50-100 horas por a&ntilde;o).</div>
					<div style="font-weight: normal;"><span style="color: red; font-weight: bolder;">Equipo suministro el&eacute;ctrico de respaldo:&nbsp;</span>Atiende los requerimientos de energ&iacute;a el&eacute;ctrica durante cortes inesperados de &eacute;sta, en posibilidad de atender capacidades y tiempos mayores a los atendidos por equipos de emergencia. El tiempo de operaci&oacute;n anual es cont&iacute;nuo por encima de 100 horas por a&ntilde;o.</div>
					<div style="font-weight: normal;"><span style="color: red; font-weight: bolder;">Planta solar:&nbsp;</span>Es la compuesta por uno o varios paneles solares los cuales toman la energ&iacute;a solar y la convierten en energ&iacute;a el&eacute;ctrica. Se pueden interconectar y se comportan como una &uacute;nica fuente de energ&iacute;a.</div>
					<div style="font-weight: normal;"><span style="color: red; font-weight: bolder;">Planta e&oacute;lica:&nbsp;</span>Es la compuesta por uno o varios generadores e&oacute;licos los cuales toman la energ&iacute;a del viento y la convierten en energ&iacute;a el&eacute;ctrica. Se pueden interconectar y se comportan como una &uacute;nica fuente de energ&iacute;a.</div>
					</fieldset>';
		}
		
		
		function __destruct() {
			mysqli_close($this->cnx);
			$this->cnx = null;
		}

		
	
	}//EOC
	
?>