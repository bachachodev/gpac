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
  $updateSQL = sprintf("UPDATE asignatura SET asigNombre=%s, asigTipo=%s, asigVigencia=%s, progCodigo=%s WHERE asigID=%s",
                       GetSQLValueString($_POST['nomAsignatura'], "text"),
					   GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString(isset($_POST['vigencia']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['programa'], "text"),
                       GetSQLValueString($_POST['asigID'], "int"));

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
$query_rsPrograma = "SELECT * FROM programa WHERE progVigencia = 1 ORDER BY progCodigo ASC";
$rsPrograma = mysql_query($query_rsPrograma, $rsPP) or die(mysql_error());
$row_rsPrograma = mysql_fetch_assoc($rsPrograma);
$totalRows_rsPrograma = mysql_num_rows($rsPrograma);

$colname_rsEdit = "-1";
if (isset($_GET['asigCodigo'])) {
  $colname_rsEdit = $_GET['asigCodigo'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM asignatura INNER JOIN programa ON asignatura.progCodigo = programa.progCodigo WHERE asigCodigo = %s", GetSQLValueString($colname_rsEdit, "text"));
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

$colname_rsProgramaVig = "-1";
if (isset($_GET['asigCodigo'])) {
  $colname_rsProgramaVig = $row_rsEdit['progCodigo'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsProgramaVig = sprintf("SELECT * FROM programa WHERE progCodigo = %s ORDER BY progCodigo ASC", GetSQLValueString($colname_rsProgramaVig, "text"));
$rsProgramaVig = mysql_query($query_rsProgramaVig, $rsPP) or die(mysql_error());
$row_rsProgramaVig = mysql_fetch_assoc($rsProgramaVig);
$totalRows_rsProgramaVig = mysql_num_rows($rsProgramaVig);
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
        <span class="sr-only">Error</span>Registro inexistente. Agregar programa
    </div> 
  <?php endif; ?>
  
  <?php //***** Alerta registro inexistente
  parse_str($_SERVER['QUERY_STRING']);
  if (isset($requsername)) : ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Programa<strong></strong> ya existe
    </div>
  <?php endif; ?>
  
  <?php //***** Alerta programa no vigente
  if (($row_rsProgramaVig['progVigencia']) == 0 ): ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Programa NO vigente
    </div>
  <?php endif; ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="editar" id="editar" role="form" autocomplete="off" autofill="off">
    <!--panel-->
    <div class="panel panel-default">
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Editar asignatura</h3>     
        <!--tab content-->
            <div class="form-group input-group" style="width:100%">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-tag" style="color:orange"></span></span>
              <select name="programa" id="programa" class="form-control" title="Programa" required/>
                
                <option <?php if (($row_rsProgramaVig['progVigencia']) == 1 ) echo "disabled" ?> value="<?php if (($row_rsProgramaVig['progVigencia']) == 0 ) {echo $row_rsEdit['progCodigo'];} else {echo "";} ?>" <?php if (($row_rsProgramaVig['progVigencia']) == 0 ) {echo ("style=\"color:red\"");} else {;} ?> <?php if ((($row_rsProgramaVig['progVigencia']) == 0 ) || (!(strcmp("", $row_rsEdit['progCodigo'])))) {echo "selected=\"selected\"";} ?>> <?php if (($row_rsProgramaVig['progVigencia']) == 0 ) {echo ("(X) ".$row_rsEdit['progNombre']);} else {echo "< Programa >";} ?> </option>
                
                <?php
				  do {  
				?>
                <option value="<?php echo $row_rsPrograma['progCodigo']?>"<?php if (!(strcmp($row_rsPrograma['progCodigo'], $row_rsEdit['progCodigo']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsPrograma['progCodigo']?> <?php echo $row_rsPrograma['progNombre']?></option>
                <?php
				  } while ($row_rsPrograma = mysql_fetch_assoc($rsPrograma));
					$rows = mysql_num_rows($rsPrograma);
					if($rows > 0) {
						mysql_data_seek($rsPrograma, 0);
						$row_rsPrograma = mysql_fetch_assoc($rsPrograma);
					}
				?>
              </select>
              <span class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php if (($row_rsProgramaVig['progVigencia']) == 1) { echo "Programa Vigente"; } else { echo "Programa NO Vigente";} ?>">
              <span class="glyphicon <?php if (($row_rsProgramaVig['progVigencia']) == 1) { echo "glyphicon-ok"; } else { echo "glyphicon-remove";} ?>" style="text-align:center; color:<?php if (($row_rsProgramaVig['progVigencia']) == 1) { echo "#0C0"; } else { echo "#F00";} ?>"></span></a></span>
            </div>
            
          <div class="form-group input-group" style="width:100%;">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-barcode" style="color:orange"></span></span>
              <input type="text" name="codigo" id="codigo" value="<?php echo $row_rsEdit['asigCodigo']; ?>" class="form-control" placeholder="<Código Asignatura>" autofocus autocomplete="off" disabled>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-bookmark" style="color:orange"></span></span>
              <select name="tipo" id="tipo" class="form-control" title="Tipo" required/>
                <option value="" <?php if (!(strcmp("", $row_rsEdit['asigTipo']))) {echo "selected=\"selected\"";} ?> disabled>Tipo</option>
                <option value="curso" <?php if (!(strcmp("curso", $row_rsEdit['asigTipo']))) {echo "selected=\"selected\"";} ?>>Curso</option>
              	<option value="taller" <?php if (!(strcmp("taller", $row_rsEdit['asigTipo']))) {echo "selected=\"selected\"";} ?>>Taller</option>              
              </select>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-file"></span></span>
              <input type="text" name="nomAsignatura" id="nomAsignatura" value="<?php echo $row_rsEdit['asigNombre']; ?>" class="form-control" placeholder="<Nombre Asignatura>" autofocus autocomplete="off">
            </div>
          <div class="checkbox checkbox-warning">
            <input <?php if (!(strcmp($row_rsEdit['asigVigencia'],1))) {echo "checked=\"checked\"";} ?> name="vigencia" type="checkbox" class="styled" id="vigencia" value="1">
            <label for="usuario">Vigente</label>
            </div>
          </div>
          <!--footer-->
        <div class="panel-footer">
        <input type="hidden" name="asigID" value="<?php echo $row_rsEdit['asigID']; ?>">
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

mysql_free_result($rsPrograma);

mysql_free_result($rsEdit);

mysql_free_result($rsProgramaVig);

?>
