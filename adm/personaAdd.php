<?php require_once('../Connections/rsPP.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "/gpac/?logout";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="personaEdit.php";
  $loginUsername = $_POST['numeroDocumento'];
  $LoginRS__query = sprintf("SELECT persNumeroDocumento FROM persona WHERE persNumeroDocumento=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_rsPP, $rsPP);
  $LoginRS=mysql_query($LoginRS__query, $rsPP) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "agregar")) {
  $insertSQL = sprintf("INSERT INTO persona (persTipoDocumento, persNumeroDocumento, persNombres, persPaterno, persMaterno, persEmail, persCelular, persTelefono, persFechaNacimiento, persDireccion, persNacionalidad, regiOrdinal, provId, comuId, persAlumno, persDocente, persGestor, persCoordinador, persUser) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['numeroDocumento'], "text"),
                       GetSQLValueString($_POST['nombres'], "text"),
                       GetSQLValueString($_POST['paterno'], "text"),
                       GetSQLValueString($_POST['materno'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['fechaNacimiento'], "date"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['nacionalidad'], "text"),
                       GetSQLValueString($_POST['region'], "text"),
                       GetSQLValueString($_POST['provincia'], "int"),
                       GetSQLValueString($_POST['comuna'], "int"),
                       GetSQLValueString(isset($_POST['alumno']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['docente']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['gestor']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['coordinador']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['usuario']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_rsPP, $rsPP);
  $Result1 = mysql_query($insertSQL, $rsPP) or die(mysql_error());

  $insertGoTo = "../home.php?addOk";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsSec = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsSec = $_SESSION['MM_Username'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsSec = sprintf("SELECT * FROM `access` WHERE acceUsr = %s", GetSQLValueString($colname_rsSec, "text"));
$rsSec = mysql_query($query_rsSec, $rsPP) or die(mysql_error());
$row_rsSec = mysql_fetch_assoc($rsSec);
$totalRows_rsSec = mysql_num_rows($rsSec);

$colname_rsPers = "-1";
if ($row_rsSec['persNumeroDocumento'] <> "") {
  $colname_rsPers = $row_rsSec['persNumeroDocumento'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsPers = sprintf("SELECT persNumeroDocumento, persNombres, persPaterno FROM persona WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsPers, "text"));
$rsPers = mysql_query($query_rsPers, $rsPP) or die(mysql_error());
$row_rsPers = mysql_fetch_assoc($rsPers);
$totalRows_rsPers = mysql_num_rows($rsPers);

$colname_rsEdit = "-1";
if (isset($_POST['editar'])) {
  $colname_rsEdit = $_POST['editar'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM persona WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsEdit, "text"));
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

mysql_select_db($database_rsPP, $rsPP);
$query_rsRegion = "SELECT * FROM regiones";
$rsRegion = mysql_query($query_rsRegion, $rsPP) or die(mysql_error());
$row_rsRegion = mysql_fetch_assoc($rsRegion);
$totalRows_rsRegion = mysql_num_rows($rsRegion);

mysql_select_db($database_rsPP, $rsPP);
$query_rsProvincia = "SELECT * FROM provincias";
$rsProvincia = mysql_query($query_rsProvincia, $rsPP) or die(mysql_error());
$row_rsProvincia = mysql_fetch_assoc($rsProvincia);
$totalRows_rsProvincia = mysql_num_rows($rsProvincia);

mysql_select_db($database_rsPP, $rsPP);
$query_rsComuna = "SELECT * FROM comunas";
$rsComuna = mysql_query($query_rsComuna, $rsPP) or die(mysql_error());
$row_rsComuna = mysql_fetch_assoc($rsComuna);
$totalRows_rsComuna = mysql_num_rows($rsComuna);

mysql_select_db($database_rsPP, $rsPP);
$query_rsPais = "SELECT * FROM paises";
$rsPais = mysql_query($query_rsPais, $rsPP) or die(mysql_error());
$row_rsPais = mysql_fetch_assoc($rsPais);
$totalRows_rsPais = mysql_num_rows($rsPais);
?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!DOCTYPE html>
<html>
<head>
<title>Agregar Registro</title>

<?php @include("../inc_assets.php"); ?>

</head>
<body>

<?php @include("../inc_nav.php"); ?>

<!--Content-->
<div class="container-fluid" style="z-index:-1">

<div class="container col-md-6 col-md-offset-3">

<?php //***** Alerta registro inexistente
if ($_SERVER['QUERY_STRING'] == "new") : ?>
  <div Id="contAlert" class="alert alert-warning fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-alert" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
      <span class="sr-only">Error</span>Registro inexistente. Agregar persona
  </div> 
<?php endif; ?>

<form method="POST" action="<?php echo $editFormAction; ?>" class="form-signin" name="agregar" id="agregar" role="form">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <!--tab header-->
      <h3>Agregar Persona</h3>
        <ul class="nav nav-tabs nav-justified">
          <li class="active"><a data-toggle="tab" href="#datos">Datos Personales</a></li>
          <li><a data-toggle="tab" href="#contacto">Contacto</a></li>
          <li><a data-toggle="tab" href="#roles">Roles</a></li>
        </ul>
      <!--tab content-->
      <div class="tab-content">
        <div id="datos" class="tab-pane fade in active">
          <br>
          <div class="form-group">
            <label>Tipo de Documento: </label><br>
            <label class="radio-inline">
              <input type="radio" name="tipo" id="rut" value="C" checked required>
              RUT
            </label> 
            <label class="radio-inline">
              <input type="radio" name="tipo" id="pasaporte" value="P" required>
              Pasaporte
            </label>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user" style="color:orange"></span></span>
            <input type="text" name="numeroDocumento" id="numeroDocumento" class="form-control" placeholder="Número de Documento" autofocus required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-flag" style="color:orange"></span></span>
            <select name="nacionalidad" id="nacionalidad" title="Nacionalidad" class="form-control selectpicker" data-live-search="true" required>
              <option disabled value="" >Nacionalidad</option>
              <option selected value="CHL" >Chile</option>
              <?php
				do {  
			  ?>
              <option value="<?php echo $row_rsPais['paisIso3']?>"><?php echo $row_rsPais['paisNombre']?></option>
              <?php
			  } while ($row_rsPais = mysql_fetch_assoc($rsPais));
				$rows = mysql_num_rows($rsPais);
				if($rows > 0) {
					mysql_data_seek($rsPais, 0);
					$row_rsPais = mysql_fetch_assoc($rsPais);
			  }
			  ?>
            </select>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left" style="color:orange"></span></span>
            <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Nombres" autofocus required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left" style="color:orange"></span></span>
            <input type="text" name="paterno" id="paterno" class="form-control" placeholder="Apellido Paterno" autofocus required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
            <input type="text" name="materno" id="materno" class="form-control" placeholder="Apellido Materno" autofocus>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control" autofocus placeholder="Fecha de Nacimiento (dd-mm-aaaa)" pattern="\d{1,2}-\d{1,2}-\d{4}" title="dd-mm-aaaa">
          </div>
        </div>
        
      <div id="contacto" class="tab-pane fade">
        <br>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-envelope"></span></span>
          <input type="email" name="email" id="email" class="form-control" placeholder="Email" autofocus>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-phone"></span></span>
          <input type="text" name="celular" id="celular" class="form-control" placeholder="Celular" autofocus>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-earphone"></span></span>
          <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono Fijo" autofocus>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
          <textarea name="direccion" id="direccion" class="form-control" placeholder="Dirección (calle, número)" autofocus></textarea>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-large"></span></span>
          <select name="region" id="region" title="Región" class="form-control" autofocus>
            <option disabled style="display:true;" value="" <?php if (!(strcmp("", "RM"))) {echo "selected=\"selected\"";} ?>>Región</option>
            <?php
			do {  
			?>
			<option value="<?php echo $row_rsRegion['regiOrdinal']?>"<?php if (!(strcmp($row_rsRegion['regiOrdinal'], "RM"))) {echo "selected=\"selected\"";} ?>><?php echo ($row_rsRegion['regiOrdinal'])?> <?php echo $row_rsRegion['regiNombre']?></option>
			<?php
			} while ($row_rsRegion = mysql_fetch_assoc($rsRegion));
			  $rows = mysql_num_rows($rsRegion);
			  if($rows > 0) {
				  mysql_data_seek($rsRegion, 0);
				  $row_rsRegion = mysql_fetch_assoc($rsRegion);
			  }
			?>
          </select>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th"></span></span>
          <select name="provincia" id="provincia" title="Provincia" class="form-control" autofocus>
            <option disabled selected value="">Provincia</option>
            <?php
			  do {  
			?>
            <option value="<?php echo $row_rsProvincia['provId']?>"><?php echo $row_rsProvincia['provNombre']?></option>
            <?php
			  } while ($row_rsProvincia = mysql_fetch_assoc($rsProvincia));
				$rows = mysql_num_rows($rsProvincia);
				if($rows > 0) {
					mysql_data_seek($rsProvincia, 0);
					$row_rsProvincia = mysql_fetch_assoc($rsProvincia);
			  }
			  ?>
          </select>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-list"></span></span>
          <select name="comuna" id="comuna" title="Comuna" class="form-control selectpicker" data-live-search="true" autofocus>
            <option disabled selected value="">Comuna</option>
            <?php
			  do {  
			?>
            <option value="<?php echo $row_rsComuna['comuId']?>"><?php echo $row_rsComuna['comuNombre']?></option>
            <?php
			} while ($row_rsComuna = mysql_fetch_assoc($rsComuna));
			  $rows = mysql_num_rows($rsComuna);
			  if($rows > 0) {
				  mysql_data_seek($rsComuna, 0);
				  $row_rsComuna = mysql_fetch_assoc($rsComuna);
			}
			?>
          </select>
        </div>
      </div>
      
      <div id="roles" class="tab-pane fade">
        <div class="checkbox checkbox-info">
          <input type="checkbox" class="styled" value="1" name="alumno" id="alumno">
          <label for="alumno">Alumno</label>
        </div>
        <div class="checkbox checkbox-info">
          <input type="checkbox" class="styled" value="1" name="docente" id="docente">
          <label for="docente">Docente</label>
        </div>
        <div class="checkbox checkbox-info">
          <input type="checkbox" class="styled" value="1" name="gestor" id="gestor">
          <label for="gestor">Gestor</label>
        </div>
        <div class="checkbox checkbox-info">
          <input type="checkbox" class="styled" value="1" name="coordinador" id="coordinador">
          <label for="coordinador">Coordinador</label>
        </div>
        <div class="checkbox checkbox-warning">
          <input type="checkbox" class="styled" value="1" name="usuario" id="usuario" <?php if ($row_rsSec['acceNivel']<10): ?>disabled<?php endif; ?>>
          <label for="usuario">Usuario</label>
        </div>
      </div>
    </div>
  </div>
    <!--footer-->
    <div class="panel-footer">
      <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Guardar</button>
    </div>
  </div>
  <input type="hidden" name="MM_insert" value="agregar">
</form>

</div>
</div>
<br>
<br>
<br>
<br>
<br>
<?php @include("../inc_session.js.php"); ?>
</body>
</html>
<?php
mysql_free_result($rsSec);

mysql_free_result($rsPers);

mysql_free_result($rsEdit);

mysql_free_result($rsRegion);

mysql_free_result($rsProvincia);

mysql_free_result($rsComuna);

mysql_free_result($rsPais);
?>
