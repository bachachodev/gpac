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
$query_rsEdit = "SELECT * FROM institucion INNER JOIN comunas ON institucion.comuId = comunas.comuId ORDER BY instNombre ASC";
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

$colname_rsComuna = "-1";
if (isset($query_rsEdit)) {
  $colname_rsComuna = $row_rsEdit['comuId'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsComuna = sprintf("SELECT * FROM comunas WHERE comuId = %s", GetSQLValueString($colname_rsComuna, "int"));
$rsComuna = mysql_query($query_rsComuna, $rsPP) or die(mysql_error());
$row_rsComuna = mysql_fetch_assoc($rsComuna);
$totalRows_rsComuna = mysql_num_rows($rsComuna);


?>

<?php ///////////////////////////////// ?>
<?php @include("../inc_session.php"); ?>
<?php ///////////////////////////////// ?>
<!DOCTYPE html>
<html>
<head>
<title>Buscar Registro</title>

<?php @include("../inc_assets.php"); ?>


</head>
<body>

<?php @include("../inc_nav.php"); ?>

<div class="container-fluid" style="z-index:-1">
<?php if ($totalRows_rsEdit > 0) : // Show if recordset no empty ?>
<div class="container col-md-6 col-md-offset-3">
  <h2>Instituciones</h2>
  <h5>(<?php echo $totalRows_rsEdit; ?> registro<?php if ($totalRows_rsEdit > 1) { echo "s" ;}?>)</h5>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th></th>
          <th>RUT</th>
          <th>Nombre</th>
          <th>Comuna</th>
          <th>Práctica</th>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr>
    <td><?php if ($_SERVER['QUERY_STRING'] == "edit") : ?>
      <a href="institucionEdit.php?instRut=<?php echo $row_rsEdit['instRut']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-pencil"></span></a>
      <?php endif; ?>
      <?php if ($_SERVER['QUERY_STRING'] == "del") : ?>
      <a href="institucionDel.php?instRut=<?php echo $row_rsEdit['instRut']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-remove"></span></a>
      <?php endif; ?>
    <td><?php echo $row_rsEdit['instRut']; ?></td>
    <td><?php echo $row_rsEdit['instNombre']; ?></td>
    <td><?php echo $row_rsEdit['comuNombre']; ?></td>
    <td><span class="glyphicon <?php if (($row_rsEdit['instPractica']) == 1) { echo "glyphicon-ok"; } else { echo "glyphicon-remove";} ?>" style="text-align:center; color:<?php if (($row_rsEdit['instPractica']) == 1) { echo "#0C0"; } else { echo "#F00";} ?>"></span></td>
  </tr>
  <?php } while ($row_rsEdit = mysql_fetch_assoc($rsEdit)); ?>
      </tbody>
    </table>
  </div>
<?php endif; // Show if recordset no empty ?>
<?php if ($totalRows_rsEdit == 0) : // Show if recordset empty ?>
  <div class="container col-md-6 col-md-offset-3" style="text-align:center">
    <h3>No hay resultados de búsqueda</h3>
  </div>
<?php endif; // Show if recordset empty ?>
</div>

  
<?php @include("../inc_session.js.php"); ?>
</body>
</html>
<?php
mysql_free_result($rsSec);

mysql_free_result($rsPers);

mysql_free_result($rsEdit);

mysql_free_result($rsComuna);
?>