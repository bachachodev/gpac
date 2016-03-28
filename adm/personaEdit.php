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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editar")) {
  $updateSQL = sprintf("UPDATE persona SET persTipoDocumento=%s, persNombres=%s, persPaterno=%s, persMaterno=%s, persEmail=%s, persCelular=%s, persTelefono=%s, persFechaNacimiento=%s, persDireccion=%s, persNacionalidad=%s, regiOrdinal=%s, provId=%s, comuId=%s, persAlumno=%s, persDocente=%s, persGestor=%s, persCoordinador=%s, persUser=%s WHERE persID=%s",
                       GetSQLValueString($_POST['tipo'], "text"),
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
                       GetSQLValueString(isset($_POST['usuario']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['persID'], "int"));

  mysql_select_db($database_rsPP, $rsPP);
  $Result1 = mysql_query($updateSQL, $rsPP) or die(mysql_error());

  $updateGoTo = "../home.php?editOk";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_rsEdit = "-1";
if (substr($_SERVER['QUERY_STRING'], 0, 11) == "requsername") { //Nuevo usuario (existente)
  $colname_rsEdit = (substr($_SERVER['QUERY_STRING'], 12, 20));
}
elseif (substr($_SERVER['QUERY_STRING'], 0, 6) == "numDoc") { //Link
  $colname_rsEdit = (substr($_SERVER['QUERY_STRING'], 7, 20));
}
else {
  $colname_rsEdit = $_POST['numeroDocumento']; //Formulario (Post)
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM persona WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsEdit, "text"));
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

mysql_select_db($database_rsPP, $rsPP);
$query_rsPais = "SELECT * FROM paises";
$rsPais = mysql_query($query_rsPais, $rsPP) or die(mysql_error());
$row_rsPais = mysql_fetch_assoc($rsPais);
$totalRows_rsPais = mysql_num_rows($rsPais);
?>

<?php if ($totalRows_rsEdit == 0) { // Show if recordset empty ?>
  <?php Header("Location: /gpac/adm/personaAdd.php?new"); //si usuario no existe?>
<?php } // Show if recordset empty ?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!doctype html>
<html>
<head>
<title>Editar Registro</title>

<?php @include("../inc_assets.php"); ?>
<script src="../scripts/jquery.Rut.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/css/login.css">

</head>
<body>

<?php @include("../inc_nav.php"); ?>

<!--Content-->
<div class="container-fluid" style="z-index:-1">

<div class="container col-md-6 col-md-offset-3">

<?php //***** Alerta registro existente
if (substr($_SERVER['QUERY_STRING'], 0, 11) == "requsername") : ?>
  <div Id="contAlert" class="alert alert-warning fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-alert" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
      <span class="sr-only">Error</span>Registro existente. Editar persona
  </div>
<?php endif; ?>

<?php if (((substr($_SERVER['QUERY_STRING'], 0, 11) == "requsername")) or (substr($_SERVER['QUERY_STRING'], 0, 6) == "numDoc") or ((isset($_POST["find"])) && ($_POST["find"] == "editar"))) : ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="editar" id="editar" role="form">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <!--tab header-->
      <h3>Editar Persona</h3>
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
              <input <?php if (!(strcmp($row_rsEdit['persTipoDocumento'],"C"))) {echo "checked=\"checked\"";} ?> type="radio" name="tipo" id="rut" value="C">
              RUT
            </label> 
            <label class="radio-inline">
              <input <?php if (!(strcmp($row_rsEdit['persTipoDocumento'],"P"))) {echo "checked=\"checked\"";} ?> type="radio" name="tipo" id="pasaporte" value="P">
              Pasaporte
            </label>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user" style="color:orange"></span></span>
            <input type="text" name="numeroDocumento" id="numeroDocumento" class="form-control" value="<?php echo $row_rsEdit['persNumeroDocumento']; ?>" readonly required>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-flag" style="color:orange"></span></span>
            <select name="nacionalidad" id="nacionalidad" title="Nacionalidad" class="form-control selectpicker" data-live-search="true" required>
              <option disabled value="" <?php if (!(strcmp("", $row_rsEdit['persNacionalidad']))) {echo "selected=\"selected\"";} ?>>Nacionalidad</option>
              <?php
				do {  
			  ?>
              <option value="<?php echo $row_rsPais['paisIso3']?>"<?php if (!(strcmp($row_rsPais['paisIso3'], $row_rsEdit['persNacionalidad']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsPais['paisNombre']?></option>
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
            <input type="text" name="nombres" id="nombres" class="form-control" value="<?php echo $row_rsEdit['persNombres']; ?>" placeholder="< Nombres >" required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left" style="color:orange"></span></span>
            <input type="text" name="paterno" id="paterno" class="form-control" value="<?php echo $row_rsEdit['persPaterno']; ?>" placeholder="< Apellido Paterno >" required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
            <input type="text" name="materno" id="materno" class="form-control" value="<?php echo $row_rsEdit['persMaterno']; ?>" placeholder="< Apellido Materno >">
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control" value="<?php echo $row_rsEdit['persFechaNacimiento']; ?>" placeholder="< Fecha de Nacimiento (dd-mm-aaaa)>" pattern="\d{1,2}-\d{1,2}-\d{4}" title="dd-mm-aaaa">
          </div>
        </div>
        
      <div id="contacto" class="tab-pane fade">
        <br>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-envelope"></span></span>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo $row_rsEdit['persEmail']; ?>" placeholder="< Correo Electrónico >" autofocus>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-phone"></span></span>
          <input type="text" name="celular" id="celular" class="form-control" value="<?php echo $row_rsEdit['persCelular']; ?>" placeholder="< Celular >">
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-earphone"></span></span>
          <input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo $row_rsEdit['persTelefono']; ?>" placeholder="< Teléfono Fijo >">
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
          <textarea name="direccion" id="direccion" class="form-control" placeholder="< Dirección (calle, número) >"><?php echo $row_rsEdit['persDireccion']; ?></textarea>
        </div>
        <div class="form-group input-group" style="width:100%">
          <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-large"></span></span>
          <select name="region" id="region" title="Región" class="form-control">
            <option disabled selected style="display:true;" value="" <?php if (!(strcmp("", $row_rsEdit['regiOrdinal']))) {echo "selected=\"selected\"";} ?>>Región</option>
            <?php
			  do {  
			?>
            <option value="<?php echo $row_rsRegion['regiOrdinal']?>"<?php if (!(strcmp($row_rsRegion['regiOrdinal'], $row_rsEdit['regiOrdinal']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsRegion['regiOrdinal'] ?> <?php echo $row_rsRegion['regiNombre']?></option>
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
          <select name="provincia" id="provincia" title="Provincia" class="form-control" >
            <option disabled selected value="" <?php if (!(strcmp("", $row_rsEdit['provId']))) {echo "selected=\"selected\"";} ?>>Provincia</option>
            <?php
			  do {  
			?>
            <option value="<?php echo $row_rsProvincia['provId']?>"<?php if (!(strcmp($row_rsProvincia['provId'], $row_rsEdit['provId']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsProvincia['provNombre']?></option>
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
          <select name="comuna" id="comuna" title="Comuna" class="form-control selectpicker" data-live-search="true">
            <option disabled selected value="" <?php if (!(strcmp("", $row_rsEdit['comuId']))) {echo "selected=\"selected\"";} ?>>Comuna</option>
            <?php
			  do {  
			?>
            <option value="<?php echo $row_rsComuna['comuId']?>"<?php if (!(strcmp($row_rsComuna['comuId'], $row_rsEdit['comuId']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsComuna['comuNombre']?></option>
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
          <input <?php if (!(strcmp($row_rsEdit['persAlumno'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" class="styled" value="1" name="alumno" id="alumno">
          <label for="alumno">Alumno</label>
        </div>
        <div class="checkbox checkbox-info">
          <input <?php if (!(strcmp($row_rsEdit['persDocente'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" class="styled" value="1" name="docente" id="docente">
          <label for="docente">Docente</label>
        </div>
        <div class="checkbox checkbox-info">
          <input <?php if (!(strcmp($row_rsEdit['persGestor'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" class="styled" value="1" name="gestor" id="gestor">
          <label for="gestor">Gestor</label>
        </div>
        <div class="checkbox checkbox-info">
          <input <?php if (!(strcmp($row_rsEdit['persCoordinador'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" class="styled" value="1" name="coordinador" id="coordinador">
          <label for="coordinador">Coordinador</label>
        </div>
        <div class="checkbox checkbox-warning">
          <input <?php if (!(strcmp($row_rsEdit['persUser'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" class="styled" value="1" name="usuario" id="usuario" <?php if ($row_rsSec['acceNivel']<10): ?>disabled<?php endif; ?>>
          <label for="usuario">Usuario</label>
        </div>
      </div>
    </div>
  </div>
    <!--footer-->
    <div class="panel-footer">
      <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Guardar</button>
    </div>
  </div>
  <input type="hidden" name="persID" value="<?php echo $row_rsEdit['persID']; ?>">
  <input type="hidden" name="MM_update" value="editar">
</form>
<?php endif ?>
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

mysql_free_result($rsRegion);

mysql_free_result($rsProvincia);

mysql_free_result($rsComuna);

mysql_free_result($rsEdit);

mysql_free_result($rsPais);
?>
