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
  $updateSQL = sprintf("UPDATE institucion SET instNombre=%s, instDireccion=%s, instContacto=%s, instTelefono=%s, instEmail=%s, instPractica=%s, comuId=%s WHERE instID=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString(isset($_POST['practica']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['comuna'], "int"),
                       GetSQLValueString($_POST['instID'], "int"));

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
$query_rsComuna = "SELECT * FROM comunas";
$rsComuna = mysql_query($query_rsComuna, $rsPP) or die(mysql_error());
$row_rsComuna = mysql_fetch_assoc($rsComuna);
$totalRows_rsComuna = mysql_num_rows($rsComuna);

$colname_rsEdit = "-1";
if (isset($_GET['instRut'])) {
  $colname_rsEdit = $_GET['instRut'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM institucion WHERE instRut = %s", GetSQLValueString($colname_rsEdit, "text"));
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);
?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!DOCTYPE html>
<html>
<head>
<title>Editar Registro</title>

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
        <span class="sr-only">Error</span>Registro inexistente. Agregar institución
    </div> 
  <?php endif; ?>
  
  <?php //***** Alerta registro existente
  parse_str($_SERVER['QUERY_STRING']);
  if (isset($requsername)) : ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>RUT<strong></strong> ya existe
    </div>
  <?php endif; ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="editar" id="editar" role="form" autocomplete="off" autofill="off">
    <!--panel-->
    <div class="panel panel-default">
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Editar institución</h3>     
        <!--tab content-->
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-barcode" style="color:orange"></span></span>
              <input type="text" name="rut" id="rut" value="<?php echo $row_rsEdit['instRut']; ?>" class="form-control" placeholder="RUT Institución" autofocus autocomplete="off" disabled>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left" style="color:orange"></span></span>
              <input type="text" name="nombre" id="nombre" value="<?php echo $row_rsEdit['instNombre']; ?>" class="form-control" placeholder="Nombre Institución" autofocus autocomplete="off" required>
            </div>
            <div class="form-group input-group" style="width:100%">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-list" style="color:orange"></span></span>
              <select name="comuna" id="comuna" class="form-control selectpicker" title="Comuna" data-live-search="true" required/>
                <option disabled selected value="">Comuna</option>
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
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
              <textarea name="direccion" id="direccion" class="form-control" placeholder="Dirección (calle, número)" autofocus autocomplete="off"><?php echo $row_rsEdit['instDireccion']; ?></textarea>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user"></span></span>
              <input type="text" name="contacto" id="contacto" value="<?php echo $row_rsEdit['instContacto']; ?>" class="form-control" placeholder="Persona de contacto" autofocus autocomplete="off">
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-earphone"></span></span>
              <input type="text" name="telefono" id="telefono" value="<?php echo $row_rsEdit['instTelefono']; ?>" class="form-control" placeholder="Teléfono" autofocus autocomplete="off">
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-envelope"></span></span>
              <input type="email" name="email" id="email" value="<?php echo $row_rsEdit['instEmail']; ?>" class="form-control" placeholder="Correo electrónico" autofocus autocomplete="off">
            </div>
            <div class="checkbox checkbox-warning">
            <input <?php if (!(strcmp($row_rsEdit['instPractica'],1))) {echo "checked=\"checked\"";} ?> name="practica" type="checkbox" class="styled" id="practica" value="1">
            <label for="usuario">Unidad de Práctica</label>
            </div>
          </div>
          <!--footer-->
        <div class="panel-footer">
        <input type="hidden" name="instID" value="<?php echo $row_rsEdit['instID']; ?>">
        <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Guardar</button>
        </div>
      </div>
    <input type="hidden" name="MM_update" value="editar">
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

mysql_free_result($rsComuna);

mysql_free_result($rsEdit);
?>
