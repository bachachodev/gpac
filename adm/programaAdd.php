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
  $MM_dupKeyRedirect="programaAdd.php";
  $loginUsername = $_POST['codigo'];
  $LoginRS__query = sprintf("SELECT progCodigo FROM programa WHERE progCodigo=%s", GetSQLValueString($loginUsername, "text"));
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
  $insertSQL = sprintf("INSERT INTO programa (progCodigo, progNombre, progAreaAcademica, progVigencia) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['nomPrograma'], "text"),
                       GetSQLValueString($_POST['areaAcademica'], "text"),
                       GetSQLValueString(isset($_POST['vigencia']) ? "true" : "", "defined","1","0"));

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
  
  <?php //***** Alerta registro existente
  parse_str($_SERVER['QUERY_STRING']);
  if (isset($requsername)) : ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Código de Programa<strong></strong> ya existe
    </div>
  <?php endif; ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="agregar" id="agregar" role="form" autocomplete="off" autofill="off">
    <!--panel-->
    <div class="panel panel-default">
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Nuevo programa</h3>     
        <!--tab content-->
            <div class="form-group input-group" style="width:100%;">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-barcode" style="color:orange"></span></span>
              <input type="text" name="codigo" id="codigo" value="" class="form-control" placeholder="Código Programa" autofocus onfocus="this.value=''" autocomplete="off" required>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-book"></span></span>
              <input type="text" name="nomPrograma" id="nomPrograma" value="" class="form-control" placeholder="Nombre Programa" autofocus onfocus="this.value=''" autocomplete="off">
            </div>
            <div class="form-group input-group" style="width:100%">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-education"></span></span>
              <input type="text" name="areaAcademica" id="areaAcademica" value="" class="form-control" placeholder="Area Académica" autofocus onfocus="this.value=''" autocomplete="off">
            </div>
            <div class="checkbox checkbox-warning">
            <input name="vigencia" type="checkbox" class="styled" id="vigencia" value="1" checked="CHECKED">
            <label for="usuario">Vigente</label>
            </div>
          </div>
          <!--footer-->
        <div class="panel-footer">
        <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Guardar</button>
        </div>
        <input type="hidden" name="MM_insert" value="agregar">
        </form>
      </div>
    </div>
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
?>
