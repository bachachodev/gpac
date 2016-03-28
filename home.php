<?php require_once('Connections/rsPP.php'); ?>
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
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "/gpac/";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
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
<?php @include("inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!doctype html>
<html>
<head>
<title>GPAC - Inicio</title>
<?php @include "inc_assets.php"; ?>

</head>
<body>

<?php @include "inc_nav.php"; ?>

<?php if (substr($_SERVER['QUERY_STRING'], 0, 5) == "addOk") : ?>
<div class="container-fluid" style="z-index:-1">
  <div class="container col-md-6 col-md-offset-3">
	<div Id="contAlert" class="alert alert-success fade in" align="center" role="alert">
	  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
	  <span class="sr-only">OK</span>Registro agregado correctamente
	</div>
  </div>
</div>
<?php endif; ?>

<?php if (substr($_SERVER['QUERY_STRING'], 0, 6) == "editOk") : ?>
<div class="container-fluid" style="z-index:-1">
  <div class="container col-md-6 col-md-offset-3">
	<div Id="contAlert" class="alert alert-success fade in" align="center" role="alert">
	  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
	  <span class="sr-only">OK</span>Registro actualizado correctamente
	</div>
  </div>
</div>
<?php endif; ?>

<?php if (substr($_SERVER['QUERY_STRING'], 0, 5) == "delOk") : ?>
<div class="container-fluid" style="z-index:-1">
  <div class="container col-md-6 col-md-offset-3">
	<div Id="contAlert" class="alert alert-success fade in" align="center" role="alert">
	  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
	  <span class="sr-only">OK</span>Registro eliminado correctamente
	</div>
  </div>
</div>
<?php endif; ?>

<?php /////////////////////////////////////////// ?>

<div class="container-fluid" style="z-index:-1">


  
</div>

<?php @include("inc_session.js.php"); ?>
</body>
</html>
<?php
mysql_free_result($rsSec);

mysql_free_result($rsPers);
?>