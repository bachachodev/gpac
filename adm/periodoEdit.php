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

<?php //*****Valores periodo
if (isset($_POST['agno'])) {
	$postAgno = $_POST['agno'];
	$postSemestre = $_POST['semestre'];
	$codPeriodo = "?";
	$descPeriodo = "?";

  switch ($postSemestre) {
	  case 1:
		  $codPeriodo = $postAgno.".".$postSemestre;
		  $descPeriodo = "1° Semestre ".$postAgno." (Otoño)";
		  break;
	  case 2:
		  $codPeriodo = $postAgno.".".$postSemestre;
		  $descPeriodo = "2° Semestre ".$postAgno." (Primavera)";
		  break;
	  case 3:
		  $codPeriodo = $postAgno.".".$postSemestre;
		  $descPeriodo = "3° Semestre ".$postAgno." (Verano)";
		  break;
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "agregar")) {
  $updateSQL = sprintf("UPDATE periodo SET periVigencia=%s WHERE periID=%s",
                       GetSQLValueString(isset($_POST['vigencia']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['periID'], "int"));

  mysql_select_db($database_rsPP, $rsPP);
  $Result1 = mysql_query($updateSQL, $rsPP) or die(mysql_error());

  $updateGoTo = "../home.php?editOk";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="periodoAdd.php";
  $loginUsername = $codPeriodo;
  $LoginRS__query = sprintf("SELECT periCodigo FROM periodo WHERE periCodigo=%s", GetSQLValueString($loginUsername, "text"));
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
if (isset($_GET['periCodigo'])) {
  $colname_rsEdit = $_GET['periCodigo'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM periodo WHERE periCodigo = %s", GetSQLValueString($colname_rsEdit, "text"));
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
        <span class="sr-only">Error</span>Registro inexistente. Agregar periodo
    </div> 
  <?php endif; ?>
  
  <?php //***** Alerta registro existente
  parse_str($_SERVER['QUERY_STRING']);
  if (isset($requsername)) : ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Periodo <strong><?php echo $requsername; ?></strong> ya existe
    </div>
  <?php endif; ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="agregar" id="agregar" role="form" autocomplete="off" autofill="off">
    <!--panel-->
    <div class="panel panel-default">
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Editar periodo</h3>     
        <!--tab content-->
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-calendar" style="color:orange"></span></span>
              <input type="text" name="agno" id="agno" value="<?php echo $row_rsEdit['periAgno']; ?>" class="form-control" placeholder="<Año>" autofocus autocomplete="off" disabled>
            </div>
            <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-tag" style="color:orange"></span></span>
            <input type="text" name="texto" value="<?php echo $row_rsEdit['periTexto']; ?>" id="texto" class="form-control" placeholder="<Semestre>" autofocus autocomplete="off" disabled>
          </div>
            <div class="checkbox checkbox-warning">
            <input <?php if (!(strcmp($row_rsEdit['periVigencia'],1))) {echo "checked=\"checked\"";} ?> name="vigencia" type="checkbox" class="styled" id="vigencia" value="1">
            <label for="usuario">Vigente</label>
            </div>
          </div>
          <!--footer-->
        <div class="panel-footer">
        <input type="hidden" name="periID" value="<?php echo $row_rsEdit['periID']; ?>">
        <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Guardar</button>
        </div>
      </div>
    <input type="hidden" name="MM_update" value="agregar">
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
?>
