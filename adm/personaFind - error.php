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

<?php //*****Valores rol
$postRol = "-1";
$rol = "";

if (isset($_POST['rol'])) {
	$postRol = $_POST['rol'];
	
	switch ($postRol) {
    case "persAlumno":
      $rol = "de Alumnos";
      break;    
    case "persDocente":
      $rol = "de Docentes";
      break;
    case "persGestor":
      $rol = "de Gestores";
      break;
    case "persCoordinador":
      $rol = "de Coordinadores";
      break;
    case "persUser":
      $rol = "de Usuarios";
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

$currentPage = $_SERVER["PHP_SELF"];

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


/////////////////////////////////////////////
//// rsEdit //////
$colname_rsEdit = "-1";
$var_rsedit = "";
$like_rsedit = "";
$case_rsedit = 0;
$acceProtect = 0;

if (empty($_POST['find'])) {
  $colname_rsEdit = "0";
  $var_rsedit = "persID";
  $like_rsedit = "";
  $case_rsedit = 0;
  $acceProtect = 0;
}
elseif (((empty($_POST['numeroDocumento'])) && (empty($_POST['paterno'])))) {
  $colname_rsEdit = "0";
  $var_rsedit = "persID";
  $like_rsedit = "";
  $case_rsedit = 0;
  $acceProtect = 0;
}
elseif ((isset($_POST['numeroDocumento'])) && (!empty($_POST['numeroDocumento'])) && (empty($_POST['paterno']))) {
  $colname_rsEdit = $_POST['numeroDocumento'];
  $var_rsedit = "persNumeroDocumento";
  $like_rsedit = "%";
  $case_rsedit = 1;
  $acceProtect = 0;
}
elseif ((empty($_POST['numeroDocumento'])) && (isset($_POST['paterno'])) && (!empty($_POST['paterno']))) {
  $colname_rsEdit = $_POST['paterno'];
  $var_rsedit = "persPaterno";
  $like_rsedit = "%";
  $case_rsedit = 2;
  $acceProtect = 0;
}
else {
  $colname_rsEdit = "0";
  $var_rsedit = "persID";
  $like_rsedit = "";
  $case_rsedit = 0;
  $acceProtect = 0;
}

mysql_select_db($database_rsPP, $rsPP);

if ((($case_rsedit = 1) || ($case_rsedit = 2)) && ($postRol == "persUser")) {
  $query_rsEdit = sprintf("SELECT * FROM persona INNER JOIN access ON persona.persNumeroDocumento = access.persNumeroDocumento WHERE persona.$var_rsedit Like %s AND persona.acceProtect = %d AND persona.$postRol = %s ORDER BY persPaterno ASC", GetSQLValueString($colname_rsEdit . $like_rsedit, "text"),GetSQLValueString($acceProtect, "int"),GetSQLValueString(1, "int"));
}
elseif ((($case_rsedit = 1) || ($case_rsedit = 2)) && ($postRol <> "persUser")) {
  $query_rsEdit = sprintf("SELECT * FROM persona WHERE $var_rsedit Like %s AND acceProtect = %d AND $postRol = %s ORDER BY persPaterno ASC", GetSQLValueString($colname_rsEdit . $like_rsedit, "text"),GetSQLValueString($acceProtect, "int"),GetSQLValueString(1, "int"));
}
else {
  $query_rsEdit = sprintf("SELECT * FROM persona WHERE $var_rsedit = %s AND acceProtect = %d AND $postRol = %s ORDER BY persPaterno ASC", GetSQLValueString($colname_rsEdit . $like_rsedit, "text"),GetSQLValueString($acceProtect, "int"),GetSQLValueString(1, "int"));
}

$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);
//////////////////////////////////

$colname_rsPersAcce = "-1";
if ((isset($_POST['numeroDocumento'])) || (isset($_POST['paterno']))) {
  $colname_rsPersAcce = $row_rsEdit['persNumeroDocumento'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsPersAcce = sprintf("SELECT * FROM access INNER JOIN persona ON access.persNumeroDocumento = persona.persNumeroDocumento WHERE ACCESS.persNumeroDocumento LIKE %s", GetSQLValueString($colname_rsPersAcce, "text"));
$rsPersAcce = mysql_query($query_rsPersAcce, $rsPP) or die(mysql_error());
$row_rsPersAcce = mysql_fetch_assoc($rsPersAcce);
$totalRows_rsPersAcce = mysql_num_rows($rsPersAcce);

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
<?php /////////////////
do {
		echo $_POST['find']."<br>";
		echo $row_rsEdit['persNumeroDocumento']." ".$row_rsEdit['acceUsr']."<br>";
	 } while ($row_rsEdit = mysql_fetch_assoc($rsEdit));
?>

<?php @include("../inc_nav.php"); ?>

<div class="container-fluid" style="z-index:-1">
<div id="form" class="container col-md-6 col-md-offset-3 <?php if ((isset($_POST['find'])) || (substr($_SERVER['QUERY_STRING'], 0, 4) == "page")) { echo "collapse";} else { echo "collapse in";} ?>">

<form method="POST" action="personaFind.php?<?php if ($_SERVER['QUERY_STRING'] == "edit") : ?>edit<?php endif; ?>
											<?php if ($_SERVER['QUERY_STRING'] == "del") : ?>del<?php endif; ?>
											<?php if ($_SERVER['QUERY_STRING'] == "adduser") : ?>adduser<?php endif; ?>
											<?php if ($_SERVER['QUERY_STRING'] == "edituser") : ?>edituser<?php endif; ?>
											<?php if ($_SERVER['QUERY_STRING'] == "deluser") : ?>deluser<?php endif; ?>                                            
                                            " class="form-signin" name="buscar" id="buscar" role="form">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <div class="form-group input-group" style="width:100%">
        <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-user"></span></span>
        <input type="text" name="numeroDocumento" id="numeroDocumento" class="form-control" onfocus="this.value=''" placeholder="Número de Documento" autofocus autocomplete="off">
      </div>
      <div class="form-group input-group" style="width:100%">
        <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-align-left"></span></span>
        <input type="text" name="paterno" id="paterno" class="form-control" onfocus="this.value=''" placeholder="Apellido Paterno" autofocus autocomplete="off">
      </div>
      <!--Roles-->
       <div id="roles" class="form-inline">
        <div class="radio radio-info">
          <input type="radio" class="form-control styled" value="persAlumno" name="rol" id="rol" <?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "disabled";} else {echo "required";} ?> >
          <label for="alumno">Alumno</label>
        </div>
        <div class="radio radio-info">
          <input type="radio" class="form-control styled" value="persDocente" name="rol" id="rol" <?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "disabled";} else {echo "required";} ?> >
          <label for="docente">Docente</label>
        </div>
        <div class="radio radio-info">
          <input type="radio" class="form-control styled" value="persGestor" name="rol" id="rol" <?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "disabled";} else {echo "required";} ?> >
          <label for="gestor">Gestor</label>
        </div>
        <div class="radio radio-info">
          <input type="radio" class="form-control styled" value="persCoordinador" name="rol" id="rol" <?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "disabled";} else {echo "required";} ?> >
          <label for="coordinador">Coordinador</label>
        </div>
        <div class="radio radio-warning">
          <input name="rol" type="radio" class="form-control styled" id="rol" value="persUser" <?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "checked";} else {echo "required";} ?> <?php if ($row_rsSec['acceNivel']<10) {echo "disabled";} ?> >
          <label for="usuario">Usuario</label>
        </div>
      </div>
      <!--Roles-->
    </div>
    <!--footer-->
    <div class="panel-footer">
      <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Buscar</button>
      <input type="hidden" name="find" value="<?php if ($_SERVER['QUERY_STRING'] == "edit") : ?>editar<?php endif; ?>
	  											<?php if ($_SERVER['QUERY_STRING'] == "del") : ?>eliminar<?php endif; ?>
												<?php if ($_SERVER['QUERY_STRING'] == "adduser") : ?>agregar<?php endif; ?>
												<?php if ($_SERVER['QUERY_STRING'] == "edituser") : ?>editar<?php endif; ?>
												<?php if ($_SERVER['QUERY_STRING'] == "deluser") : ?>eliminar<?php endif; ?>
                                              ">
    </div>
  </div>
</form>

</div>
</div>

<?php if ($totalRows_rsEdit > 0) { // Show if recordset not empty ?>
  <div class="container col-md-6 col-md-offset-3">
    <a href="#form" data-toggle="collapse" title="Nueva búsqueda"><span class="glyphicon glyphicon-search"></span> Nueva búsqueda</a>
    <h2>Resultados búsqueda <?php if ($_SERVER['QUERY_STRING'] == "adduser") {echo "con Rol ".$rol;} else {echo $rol;} ?></h2>
    <h5>(<?php echo $totalRows_rsEdit ?> registro<?php if ($totalRows_rsEdit > 1) { echo "s" ;}?>)</h5>
    <div class="table-responsive">     
      <table class="table table-hover">
        <thead>
          <tr>
            <th></th>
            <th>Número Documento</th>
            <th>Paterno</th>
            <th>Nombres</th>
            <th><?php if (($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) {echo "Usuario";} else {echo "Nacionalidad";} ?></th>
          </tr>
        </thead>
        <tbody>
		  <?php do { ?>
            <tr>
              <td>
              <?php if ($_SERVER['QUERY_STRING'] == "edit") : ?>
                <a href="personaEdit.php?numDoc=<?php echo $row_rsEdit['persNumeroDocumento']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-pencil"></span></a>
			  <?php endif; ?>
			  <?php if ($_SERVER['QUERY_STRING'] == "del") : ?>
                <a href="personaDel.php?numDoc=<?php echo $row_rsEdit['persNumeroDocumento']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-remove"></span></a>
			  <?php endif; ?>
              <?php if ($_SERVER['QUERY_STRING'] == "adduser") : ?>
                <a href="usuarioAdd.php?numDoc=<?php echo $row_rsEdit['persNumeroDocumento']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-user"></span>+</a>
			  <?php endif; ?></strong>
              <?php if ($_SERVER['QUERY_STRING'] == "edituser") : ?>
                <a href="usuarioEdit.php?numDoc=<?php echo $row_rsEdit['persNumeroDocumento']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-pencil"></span></a>
			  <?php endif; ?>
			  <?php if ($_SERVER['QUERY_STRING'] == "deluser") : ?>
                <a href="usuarioDel.php?numDoc=<?php echo $row_rsEdit['persNumeroDocumento']; ?>" data-toggle="tooltip" data-placement="top" title="Editar" onclick="submitForm()"><span class="glyphicon glyphicon-remove"></span></a>
			  <?php endif; ?>
              </td>
              <td><?php echo $row_rsEdit['persNumeroDocumento']; ?></td>
              <td><?php echo $row_rsEdit['persPaterno']; ?></td>
              <td><?php echo $row_rsEdit['persNombres']; ?></td>
              <td><?php if ((($_SERVER['QUERY_STRING'] == "adduser") || ($_SERVER['QUERY_STRING'] == "edituser") || ($_SERVER['QUERY_STRING'] == "deluser")) && ($row_rsPersAcce['acceUsr'] <> "")) {echo "<span class=\"glyphicon glyphicon-ok\" style=\"color:#0C0\"></span>";} else {echo "<span class=\"glyphicon glyphicon-remove\" style=\"color:#F00\"></span>";} ?></td>
            </tr>
            <?php } while ($row_rsEdit = mysql_fetch_assoc($rsEdit)); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php } // Show if recordset not empty ?>
  
  <?php if ((isset($_POST['find'])) && (($_POST['numeroDocumento'] <> "") || ($_POST['paterno'] <> "")) && ($totalRows_rsEdit == 0)) : // Show if recordset empty ?>
  <div class="container col-md-6 col-md-offset-3" style="text-align:center">
    <h3>No hay resultados de búsqueda</h3>
    <a href="#form" data-toggle="collapse" title="Nueva búsqueda"><span class="glyphicon glyphicon-search"></span> Nueva búsqueda</a>
    </div>
  <?php endif; // Show if recordset empty ?>
  
  <?php if ((isset($_POST['find'])) && (empty($_POST['numeroDocumento'])) && (empty($_POST['paterno']))) : // Show if recordset empty ?>
  <div class="container col-md-6 col-md-offset-3" style="text-align:center">
    <h3>Debe ingresar al menos un parámetro de búsqueda</h3>
    <a href="#form" data-toggle="collapse" title="Nueva búsqueda"><span class="glyphicon glyphicon-search"></span> Nueva búsqueda</a>
    </div>
  <?php endif; // Show if recordset empty ?>
  
<?php @include("../inc_session.js.php"); ?>
</body>
</html>
<?php
mysql_free_result($rsSec);

mysql_free_result($rsPers);

mysql_free_result($rsPersAcce);

mysql_free_result($rsEdit);
?>