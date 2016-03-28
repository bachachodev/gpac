<?php require_once('Connections/rsPP.php');?>
<?php
// *** Logout the current user.
$logoutGoTo = "";
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['PrevUrl'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['PrevUrl']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['secUsr'])) {
  $loginUsername=$_POST['secUsr'];
  $password=$_POST['secPwd'];
  $MM_fldUserAuthorization = "acceNivel";
  $MM_redirectLoginSuccess = "home.php";
  $MM_redirectLoginFailed = "?error";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_rsPP, $rsPP);
  	
  $LoginRS__query=sprintf("SELECT acceUsr, accePwd, acceNivel FROM `access` WHERE acceUsr=%s AND accePwd=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $rsPP) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'acceNivel');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>GPAC - Login</title>
<?php @include("inc_assets.php"); ?>
<link rel="stylesheet" type="text/css" href="scripts/css/login.css">

</head>
<body>
<div class="container-fluid">
	<div Id="contLogin" class="img-rounded">
    	<div class="form-group" style="font-size:16px; font-weight:bold" align="center">GESTION DE PRACTICAS ACADEMICAS</div>
    	<div class="card card-container; form-group" align="center">
    		<img src="images/LogoDEP128.png" class="profile-img-card" id="profile-img">
            <form class="form-signin" role="form" ACTION="<?php echo $loginFormAction; ?>" method="POST" enctype="application/x-www-form-urlencoded" name="login-form">
        	<div class="form-group input-group">
            	<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user"></span></span>
        		<input type="text" name="secUsr" id="inputUser" class="form-control" aria-describedby="basic-addon1" onfocus="this.value=''" placeholder="Usuario" autofocus autocomplete="off" required/>
            </div>
			<div class="form-group input-group">
            	<span class="input-group-addon" id="basic-addon2"><span class="glyphicon glyphicon-lock"></span></span>
            	<input type="password" name="secPwd" id="inputPassword" class="form-control" aria-describedby="basic-addon2" onfocus="this.value=''" placeholder="Contraseña" required>
            </div>
            <br>
            <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Login</button>
    	</form><!-- /form --> 
        </div><!-- /card-container -->

	</div><!-- /contLogin -->
    	<?php if ($_SERVER['QUERY_STRING'] == "error") : ?>
			<div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
				<span class="sr-only">Error</span>Error de Usuario o Contraseña
			</div>
		<?php endif; ?>
        
        <?php if (substr($_SERVER['QUERY_STRING'], 0, 11) == "accesscheck") : ?>
			<div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
				<span class="sr-only">Error</span>ACCESO DENEGADO
			</div>
		<?php endif; ?>
        
    	<?php if ($_SERVER['QUERY_STRING'] == "logout") : ?>
			<div Id="contAlert" class="alert alert-success fade in" align="center" role="alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
				<span class="sr-only">Exit</span>Sesión cerrada correctamente
			</div>
		<?php endif; ?>
        
        <?php if ($_SERVER['QUERY_STRING'] == "timeout") : ?>
			<div Id="contAlert" class="alert alert-warning fade in" align="center" role="alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<span class="glyphicon glyphicon-alert" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>
				<span class="sr-only">Error</span>Sesión Expirada. Vuelva a ingresar
			</div>
		<?php endif; ?>              
</div><!-- /container -->

</body>
</html>