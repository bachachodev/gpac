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

<?php //*****Valores seccion
if (isset($_POST['asignatura'])) {
	$postAsignatura = $_POST['asignatura'];
	$postSeccNumero = $_POST['seccNumero'];
	$codSeccion = $postAsignatura.".".$postSeccNumero;
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
  $MM_dupKeyRedirect="seccionAdd.php";
  $loginUsername = $codSeccion;
  $LoginRS__query = sprintf("SELECT seccCodigo FROM seccion WHERE seccCodigo=%s", GetSQLValueString($loginUsername, "text"));
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
  $insertSQL = sprintf("INSERT INTO seccion (seccCodigo, asigCodigo, seccNumero, secNumDocDocente, espeCodigo, seccTipo, seccVigencia) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($codSeccion, "text"),
                       GetSQLValueString($_POST['asignatura'], "text"),
                       GetSQLValueString($_POST['seccNumero'], "int"),
                       GetSQLValueString($_POST['docente'], "text"),
                       GetSQLValueString($_POST['especialidad'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
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

mysql_select_db($database_rsPP, $rsPP);
$query_rsAsignatura = "SELECT * FROM asignatura WHERE asigVigencia = 1";
$rsAsignatura = mysql_query($query_rsAsignatura, $rsPP) or die(mysql_error());
$row_rsAsignatura = mysql_fetch_assoc($rsAsignatura);
$totalRows_rsAsignatura = mysql_num_rows($rsAsignatura);

mysql_select_db($database_rsPP, $rsPP);
$query_rsDocente = "SELECT * FROM persona WHERE persDocente = 1 ORDER BY persPaterno, persNombres ASC";
$rsDocente = mysql_query($query_rsDocente, $rsPP) or die(mysql_error());
$row_rsDocente = mysql_fetch_assoc($rsDocente);
$totalRows_rsDocente = mysql_num_rows($rsDocente);

mysql_select_db($database_rsPP, $rsPP);
$query_rsEspecialidad = "SELECT * FROM especialidad WHERE espeVigencia = 1";
$rsEspecialidad = mysql_query($query_rsEspecialidad, $rsPP) or die(mysql_error());
$row_rsEspecialidad = mysql_fetch_assoc($rsEspecialidad);
$totalRows_rsEspecialidad = mysql_num_rows($rsEspecialidad);

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
        <span class="sr-only">Error</span>Registro inexistente. Agregar sección
    </div> 
  <?php endif; ?>
  
  <?php //***** Alerta registro existente
  parse_str($_SERVER['QUERY_STRING']);
  if (isset($requsername)) : ?>
    <div Id="contAlert" class="alert alert-danger fade in" align="center" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-remove" aria-hidden="true" style="font-size:22px; vertical-align:middle"></span>Código de Sección <strong><?php echo $requsername; ?></strong> ya existe
    </div>
  <?php endif; ?>

<form action="<?php echo $editFormAction; ?>" method="POST" class="form-signin" name="agregar" id="agregar" role="form" autocomplete="off" autofill="off">
    <!--panel-->
    <div class="panel panel-default">
      <!--body-->
      <div class="panel-body">
        <!--tab header-->
        <h3>Nueva sección</h3>
        <!--tab content-->
            <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-list-alt" style="color:orange"></span></span>
            <select name="asignatura" id="asignatura" class="form-control" title="Asignatura" required/>
              <option disabled selected value="">Asignatura</option>
			  <?php
              do {  
              ?>
              <option value="<?php echo $row_rsAsignatura['asigCodigo']?>"><?php echo $row_rsAsignatura['asigCodigo']?> <?php echo $row_rsAsignatura['asigNombre']?></option>
              <?php
				} while ($row_rsAsignatura = mysql_fetch_assoc($rsAsignatura));
				  $rows = mysql_num_rows($rsAsignatura);
				  if($rows > 0) {
					  mysql_data_seek($rsAsignatura, 0);
					  $row_rsAsignatura = mysql_fetch_assoc($rsAsignatura);
				  }
              ?>
            </select>
            </div>
            <div class="form-group input-group" style="width:100%;">
              <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-list" style="color:orange"></span></span>
              <input type="text" name="seccNumero" id="seccNumero" value="" class="form-control" placeholder="Número Sección" autofocus autocomplete="off" required/>
            </div>
            <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user"></span></span>
            <select name="docente" id="docente" class="form-control" title="Docente">
              <option disabled selected value="">Docente</option>
			  <?php
              do {  
              ?>
              <option value="<?php echo $row_rsDocente['persNumeroDocumento']; ?>"><?php echo $row_rsDocente['persPaterno']; ?>, <?php echo $row_rsDocente['persNombres']; ?></option>
              <?php
				} while ($row_rsDocente = mysql_fetch_assoc($rsDocente));
				  $rows = mysql_num_rows($rsDocente);
				  if($rows > 0) {
					  mysql_data_seek($rsDocente, 0);
					  $row_rsDocente = mysql_fetch_assoc($rsDocente);
				  }
              ?>
            </select>
            </div>
            <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-star"></span></span>
            <select name="especialidad" id="especialidad" class="form-control" title="Especialidad">
              <option disabled selected value="">Especialidad</option>
			  <?php
              do {  
              ?>
              <option value="<?php echo $row_rsEspecialidad['espeCodigo']; ?>"><?php echo $row_rsEspecialidad['espeNombre']; ?></option>
              <?php
				} while ($row_rsEspecialidad = mysql_fetch_assoc($rsEspecialidad));
				  $rows = mysql_num_rows($rsEspecialidad);
				  if($rows > 0) {
					  mysql_data_seek($rsEspecialidad, 0);
					  $row_rsEspecialidad = mysql_fetch_assoc($rsEspecialidad);
				  }
              ?>
            </select>
            </div>
            <div class="form-group input-group" style="width:100%">
            <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-tag"></span></span>
            <select name="tipo" id="tipo" class="form-control" title="Tipo Sección">
              <option disabled selected value="">Tipo Sección</option>
              <option value="Practica">Práctica</option>
              <option value="Taller">Taller</option>
            </select>
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

mysql_free_result($rsAsignatura);

mysql_free_result($rsDocente);

mysql_free_result($rsEspecialidad);
?>
