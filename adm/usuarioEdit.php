<?php require_once('../Connections/rsPP.php'); ?>
<?php parse_str($_SERVER['QUERY_STRING']); //string a variables?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editar") && (isset($pwd))) { //editar solo contraseña
  $updateSQL = sprintf("UPDATE `access` SET accePwd=%s WHERE acceID=%d",
					   GetSQLValueString($_POST['pwd'], "text"),
					   GetSQLValueString($_POST['acceID'], "int"));
}
elseif ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editar")) {
  $updateSQL = sprintf("UPDATE `access` SET accePwd=%s, acceNivel=%d, acceDescripcion=%s WHERE acceID=%d",
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['nivel'], "int"),
                       GetSQLValueString($descUser, "text"),
                       GetSQLValueString($_POST['acceID'], "int"));
}

if (isset($updateSQL)) {
  $updateSQL;
					   
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

$colname_rsEdit = "-1";
if (isset($_GET['numDoc'])) {
  $colname_rsEdit = $_GET['numDoc'];
  $protect_rsEdit = "0";
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
<title>Editar Registro</title>

<?php @include("../inc_assets.php"); ?>

</head>
<body>
<?php //echo $_SERVER['QUERY_STRING']; ?>
<?php //(parse_str($_SERVER['QUERY_STRING'])); ?>
<?php //echo $numDoc; ?>
<?php //echo $requsername; ?>
<?php //echo (substr($_SERVER['QUERY_STRING'], 0, 6)); ?>
<?php //echo $row_rsEdit['persNumeroDocumento']; ?>
<?php //echo $row_rsEdit['acceUsr']; ?>
<?php //echo $row_rsEdit['accePwd']; ?>
<?php //echo $colname_rsEdit; ?>
<?php //echo $colname_rsPersUser; ?>


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

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="editar" id="editar" role="form" autocomplete="off" autofill="off">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <!--tab header-->
      <h3>
	  <?php //***** Si existe usuario creado
		if ((isset($pwd)) && ($row_rsSec['acceProtect'] == 1)){
		  echo "Usuario Protegido";
		}
		elseif ($totalRows_rsEdit == 0) {
		  echo "Esta persona no tiene un usuario creado";
		}
		elseif (isset($pwd)){
		  echo "Editar contraseña usuario";
		}
		else {
		  echo "Editar usuario";
		}
	  ?>
	  </h3>
      <span style="text-transform:uppercase; color:orange;"><h5><strong><?php echo $row_rsPersUser['persNombres']; ?> <?php echo $row_rsPersUser['persPaterno']; ?> <?php echo $row_rsPersUser['persMaterno']; ?></strong></h5></span>
      
      <!--tab content-->
      <?php if ($totalRows_rsEdit > 0) : //**** Si existe usuario creado ?>
      <div class="tab-content">
        <div id="datos" class="tab-pane fade in active">
          <div class="form-group input-group" style="width:100%;">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user" style="color:orange"></span></span>
            <input type="text" name="viewUser" id="viewUser" value="<?php if (isset($pwd)) { echo ($row_rsSec['acceUsr']); } else { echo ($row_rsEdit['acceUsr']); } ?>" class="form-control" placeholder="<Usuario>" autofocus autocomplete="new-user" disabled required/>
          </div>
          <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left" style="color:orange"></span></span>
            <input type="password" name="pwd" id="pwd" value="<?php if (isset($pwd)) { echo ($row_rsSec['accePwd']); } else { echo ($row_rsEdit['accePwd']); } ?>" class="form-control" placeholder="<Contraseña>" autofocus autocomplete="new-password" required/>
          </div>

          <div class="form-group input-group" style="width:100%; <?php if (isset($pwd)) : ?>visibility:hidden;<?php endif; ?>">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-tag" style="color:orange"></span></span>
            <select name="nivel" id="nivel" class="form-control" title="Tipo Usuario" required/>
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
      <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Guardar</button>
    </div>
    <?php endif; ?>
  </div>
  <input type="hidden" name="acceID" value="<?php if (isset($pwd)) { echo ($row_rsSec['acceID']); } else { echo ($row_rsEdit['acceID']); } ?>">
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

mysql_free_result($rsPersUser);

mysql_free_result($rsEdit);
?>
