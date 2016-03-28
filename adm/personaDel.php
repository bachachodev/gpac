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

if ((isset($_POST['persID'])) && ($_POST['persID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM persona WHERE persID=%s",
                       GetSQLValueString($_POST['persID'], "int"));

  mysql_select_db($database_rsPP, $rsPP);
  $Result1 = mysql_query($deleteSQL, $rsPP) or die(mysql_error());

  $deleteGoTo = "../home.php?delOk";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
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
if (substr($_SERVER['QUERY_STRING'], 0, 6) == "numDoc") { //Link
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

$colname_rsAccess = "-1";
if (isset($_POST['numeroDocumento'])) {
  $colname_rsAccess = $_POST['numeroDocumento'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsAccess = sprintf("SELECT * FROM `access` WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsAccess, "text"));
$rsAccess = mysql_query($query_rsAccess, $rsPP) or die(mysql_error());
$row_rsAccess = mysql_fetch_assoc($rsAccess);
$totalRows_rsAccess = mysql_num_rows($rsAccess);

$colname_rsUser = "-1";
if (substr($_SERVER['QUERY_STRING'], 0, 6) == "numDoc") { //Link
  $colname_rsUser = (substr($_SERVER['QUERY_STRING'], 7, 20));
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsUser = sprintf("SELECT * FROM `access` WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $rsPP) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);
?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!doctype html>
<html>
<head>
<title>Eliminar Registro</title>

<?php @include("../inc_assets.php"); ?>
<script src="../scripts/jquery.Rut.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/css/login.css">

</head>
<body>

<?php @include("../inc_nav.php"); ?>

<!--Content-->
<div class="container-fluid" style="z-index:-1">

<div class="container col-md-6 col-md-offset-3">
<?php if ($totalRows_rsEdit == 0) { // Show if recordset empty ?>
<!--Alerta registro existente-->
  <div Id="contAlert" class="alert alert-warning fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-alert" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
      <span class="sr-only">Error</span>Registro no existe o fue eliminado.
  </div>
<?php } // Show if recordset empty ?>

<?php if ($totalRows_rsEdit > 0) { // Show if recordset not empty ?>
<?php if ($totalRows_rsUser > 0) : ?>
  <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-alert" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
      <span class="sr-only">Error</span>Existe<?php if ($totalRows_rsUser > 1) {echo "n";} ?> <?php echo $totalRows_rsUser ?> usuario<?php if ($totalRows_rsUser > 1) {echo "s";} ?> vinculado<?php if ($totalRows_rsUser > 1) {echo "s";} ?> a esta persona.<br>No se puede eliminar este registro
  </div>
<?php endif ?>

<?php if (((isset($_POST["find"])) && ($_POST["find"] == "eliminar")) or (substr($_SERVER['QUERY_STRING'], 0, 6) == "numDoc")) : ?>
  
  <form method="POST" class="form-signin" name="eliminar" id="eliminar" role="form">
    <!--panel-->
    <div class="panel panel-default"> 
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Eliminar Persona</h3>
        <!--tab content-->
        <div class="tab-content">
            
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:100px"><?php if ($row_rsEdit['persTipoDocumento'] == "C") :?>RUT<?php endif ?><?php if ($row_rsEdit['persTipoDocumento'] == "P") :?>Pasaporte<?php endif ?></span>
            <input type="text" class="form-control" value="<?php echo $row_rsEdit['persNumeroDocumento']; ?>" disabled>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:100px"><span class="glyphicon glyphicon-flag"></span></span>
            <select name="nacionalidad" id="nacionalidad" class="form-control" title="Nacionalidad" disabled>
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
            <span class="input-group-addon" style="width:100px"><span class="glyphicon glyphicon-align-left"></span></span>
            <input type="text" class="form-control" value="<?php echo $row_rsEdit['persNombres']; ?> <?php echo $row_rsEdit['persPaterno']; ?> <?php echo $row_rsEdit['persMaterno']; ?>" placeholder="?" disabled>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:100px"><span class="glyphicon glyphicon-envelope"></span></span>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo $row_rsEdit['persEmail']; ?>" placeholder="< Correo Electrónico >" disabled>
          </div>
          
          <?php if ($totalRows_rsUser > 0) : ?>
          <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
            <strong>Usuario<?php if ($totalRows_rsUser > 1) {echo "s";} ?> vinculado<?php if ($totalRows_rsUser > 1) {echo "s";} ?>:</strong> <?php do { ?><?php echo (" |".($row_rsUser['acceUsr'])."| "); ?> <?php } while ($row_rsUser = mysql_fetch_assoc($rsUser)); ?>
          </div>
		  <?php endif ?>
          
        </div>
        <!--footer-->
        <div class="panel-footer">
          <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit" <?php if ($totalRows_rsUser > 0) : ?>disabled<?php endif ?>>Eliminar</button>
        </div>
      </div>
    <input type="hidden" name="persID" value="<?php echo $row_rsEdit['persID']; ?>">
  </form>
  <?php endif ?>
  <?php } // Show if recordset not empty ?>
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

mysql_free_result($rsPais);

mysql_free_result($rsAccess);

mysql_free_result($rsUser);
?>
