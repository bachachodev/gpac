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
<?php //*****Valores Descripcion
if (isset($_POST['nivel'])) {
	$postNivel = $_POST['nivel'];
	$descUser = "?";

  switch ($postNivel) {
	  case 5:
		  $descUser = "Gestor";
		  break;
	  case 7:
		  $descUser = "Coordinador";
		  break;
	  case 10:
		  $descUser = "Administrador";
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

if ((isset($_POST['acceID'])) && ($_POST['acceID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM `access` WHERE acceID=%s",
                       GetSQLValueString($_POST['acceID'], "int"));

  mysql_select_db($database_rsPP, $rsPP);
  $Result1 = mysql_query($deleteSQL, $rsPP) or die(mysql_error());

  $deleteGoTo = "../home.php?delOk";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="usuarioAdd.php?numDoc=".$_POST['numeroDocumento'];
  $loginUsername = $_POST['user'];
  $LoginRS__query = sprintf("SELECT acceUsr FROM `access` WHERE acceUsr=%s", GetSQLValueString($loginUsername, "text"));
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
if (isset($_GET['numDoc'])) {
  $colname_rsEdit = $_GET['numDoc'];
}
$protect_rsEdit = "-1";
if (isset($_GET['numDoc'])) {
  $protect_rsEdit = 0;
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsEdit = sprintf("SELECT * FROM `access` WHERE persNumeroDocumento = %s and acceProtect = %d", GetSQLValueString($colname_rsEdit, "text"),GetSQLValueString($protect_rsEdit, "int"));
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

$colname_rsPersUser = "-1";
if (isset($colname_rsEdit)) {
  $colname_rsPersUser = $colname_rsEdit;
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsPersUser = sprintf("SELECT * FROM persona WHERE persNumeroDocumento = %s", GetSQLValueString($colname_rsPersUser, "text"));
$rsPersUser = mysql_query($query_rsPersUser, $rsPP) or die(mysql_error());
$row_rsPersUser = mysql_fetch_assoc($rsPersUser);
$totalRows_rsPersUser = mysql_num_rows($rsPersUser);
?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!DOCTYPE html>
<html>
<head>
<title>Eliminar Registro</title>

<?php @include("../inc_assets.php"); ?>

</head>
<body>

<?php @include("../inc_nav.php"); ?>

<!--Content-->
<div class="container-fluid" style="z-index:-1">

<div class="container col-md-6 col-md-offset-3">

<?php parse_str($_SERVER['QUERY_STRING']);
if (isset($requsername)) : ?>
  <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Nombre de usuario <strong>< <?php echo $requsername; ?> ></strong> ya existe
  </div>
<?php endif; ?>

<form method="POST" class="form-signin" name="eliminar" id="eliminar" role="form" autocomplete="off" autofill="off">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <!--tab header-->
      <h3>
	  <?php //***** Si existe usuario creado
		if ($totalRows_rsEdit == 0) {
		  echo "Esta persona no tiene un usuario creado";
		}
		else {
		  echo "Eliminar usuario";
		}
	  ?>
	  </h3>
      <span style="text-transform:uppercase; color:orange;"><h5><strong><?php echo $row_rsPersUser['persNombres']; ?> <?php echo $row_rsPersUser['persPaterno']; ?> <?php echo $row_rsPersUser['persMaterno']; ?></strong><br>
      (<?php echo $row_rsPersUser['persTipoDocumento']; ?> <?php echo $row_rsPersUser['persNumeroDocumento']; ?>)</h5></span>
      
      <!--tab content-->
      <?php if ($totalRows_rsEdit > 0) : //**** Si existe usuario creado ?>
      <div class="tab-content">
        <div id="datos" class="tab-pane fade in active">
          <div class="form-group input-group" style="width:100%;">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user" style="color:orange"></span></span>
            <input type="text" name="viewUser" id="viewUser" value="<?php echo $row_rsEdit['acceUsr']; ?>" class="form-control" placeholder="<Usuario>" autofocus autocomplete="new-user" disabled required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-tag" style="color:orange"></span></span>
            <select name="nivel" id="nivel" class="form-control" title="Tipo Usuario" disabled required/>
              <option value="" disabled <?php if (!(strcmp(0, $row_rsEdit['acceNivel']))) {echo "selected=\"selected\"";} ?>>Tipo de usuario</option>
              <option value="5" <?php if (!(strcmp(5, $row_rsEdit['acceNivel']))) {echo "selected=\"selected\"";} ?>>Gestor</option>
              <option value="7" <?php if (!(strcmp(7, $row_rsEdit['acceNivel']))) {echo "selected=\"selected\"";} ?>>Coordinador</option>
              <option value="10" <?php if (!(strcmp(10, $row_rsEdit['acceNivel']))) {echo "selected=\"selected\"";} ?>>Administrador</option>
            </select>
          </div>
        </div>
    </div>
  </div>
    <!--footer-->
    <div class="panel-footer">
      <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Eliminar</button>
    </div>
    <?php endif; ?>
  </div>
  <input type="hidden" name="acceID" value="<?php echo $row_rsEdit['acceID']; ?>">
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

mysql_free_result($rsPersUser);

mysql_free_result($rsEdit);
?>
