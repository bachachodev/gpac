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
$query_rsEdit = "SELECT * FROM seccion ORDER BY seccCodigo ASC";
$rsEdit = mysql_query($query_rsEdit, $rsPP) or die(mysql_error());
$row_rsEdit = mysql_fetch_assoc($rsEdit);
$totalRows_rsEdit = mysql_num_rows($rsEdit);

$asignatura_rsSeccion = "-1";
if (isset($_POST['asignatura'])) {
  $asignatura_rsSeccion = $_POST['asignatura'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsSeccion = sprintf("SELECT * FROM asignatura INNER JOIN seccion ON asignatura.asigCodigo = seccion.asigCodigo INNER JOIN especialidad ON seccion.espeCodigo = especialidad.espeCodigo WHERE seccion.asigCodigo = %s ORDER BY asignatura.asigCodigo ASC", GetSQLValueString($asignatura_rsSeccion, "text"));
$rsSeccion = mysql_query($query_rsSeccion, $rsPP) or die(mysql_error());
$row_rsSeccion = mysql_fetch_assoc($rsSeccion);
$totalRows_rsSeccion = mysql_num_rows($rsSeccion);

$programa_rsAsignatura = "-1";
if (isset($_POST['programa'])) {
  $programa_rsAsignatura = $_POST['programa'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_rsAsignatura = sprintf("SELECT * FROM asignatura WHERE progCodigo = %s", GetSQLValueString($programa_rsAsignatura, "text"));
$rsAsignatura = mysql_query($query_rsAsignatura, $rsPP) or die(mysql_error());
$row_rsAsignatura = mysql_fetch_assoc($rsAsignatura);
$totalRows_rsAsignatura = mysql_num_rows($rsAsignatura);

mysql_select_db($database_rsPP, $rsPP);
$query_rsPrograma = "SELECT * FROM programa";
$rsPrograma = mysql_query($query_rsPrograma, $rsPP) or die(mysql_error());
$row_rsPrograma = mysql_fetch_assoc($rsPrograma);
$totalRows_rsPrograma = mysql_num_rows($rsPrograma);


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

<?php ///////////////////////////////// ?>
<div class="container-fluid" style="z-index:-1">
Programa: <?php if (isset($_POST['programa'])) {echo "SET ".$_POST['programa'];} else{"?";}?><br>
Asignatura: <?php if (isset($_POST['asignatura'])) {echo "SET ".$_POST['asignatura'];} else{"?";}?><br>
Variables Programa:<br> 
Programa.progCodigo (<?php echo $row_rsPrograma['progCodigo']?>)<br>
Asignatura.progCodigo (<?php echo $row_rsAsignatura['progCodigo']?>)<br>
Seccion.progCodigo (<?php echo $row_rsSeccion['progCodigo']?>)<br>
<?php ///////////////////////////////// ?>

<div id="form" class="container col-md-6 col-md-offset-3 <?php if ((isset($_POST['find'])) || (substr($_SERVER['QUERY_STRING'], 0, 4) == "page")) { echo "collapse";} else { echo "collapse in";} ?>">

<form method="POST" action="practicaSeccionFind.php?<?php if ($_SERVER['QUERY_STRING'] == "addprac") : ?>addprac<?php endif; ?><?php if ($_SERVER['QUERY_STRING'] == "editprac") : ?>editprac<?php endif; ?><?php if ($_SERVER['QUERY_STRING'] == "delprac") : ?>delprac<?php endif; ?>" class="form-signin" name="buscar" id="buscar" role="form">
  <!--panel-->
  <div class="panel panel-default">
    <!--body-->
    <div class="panel-body">
      <div class="form-group input-group" style="width:100%">
        <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-list-alt"></span></span>
        <select name="programa" id="programa" class="form-control" title="Programa" onChange="this.form.submit();" <?php if (isset($_POST['programa'])) {echo "disabled";} ?>>
          <option disabled value="" <?php if ((isset($_POST['programa'])) || (isset($_POST['asignatura']))) {echo "";} else {echo "selected";} ?>>Programa</option>
          <?php
          do {  
          ?>
          <option value="<?php if (isset($_POST['asignatura'])) {echo $_POST['programa'];} elseif (isset($_POST['programa'])) {echo $_POST['programa'];} else {echo $row_rsPrograma['progCodigo'];} ?>"
		  <?php if (isset($_POST['asignatura']))
			  {
			  if(((!(strcmp($row_rsSeccion['progCodigo'], $row_rsAsignatura['progCodigo']))) && ($totalRows_rsSeccion == 0)) || (!(strcmp($row_rsSeccion['progCodigo'], $row_rsAsignatura['progCodigo'])))) {
				  echo "selected";
				} 
			  }
			  elseif (isset($_POST['programa']))
			  {
			  if(((!(strcmp($row_rsAsignatura['progCodigo'], $row_rsPrograma['progCodigo']))) && ($totalRows_rsAsignatura == 0)) || (!(strcmp($row_rsAsignatura['progCodigo'], $row_rsPrograma['progCodigo'])))){
				  echo "selected";
				}
			  }
		  ?>
		  <?php if ((isset($_POST['programa'])) || (isset($_POST['asignatura']))) {echo "disabled";} ?>>
		  <?php echo $row_rsPrograma['progCodigo']?> <?php echo $row_rsPrograma['progNombre']?></option>
          <?php
            } while ($row_rsPrograma = mysql_fetch_assoc($rsPrograma));
              $rows = mysql_num_rows($rsPrograma);
              if($rows > 0) {
                  mysql_data_seek($rsPrograma, 0);
                  $row_rsPrograma = mysql_fetch_assoc($rsPrograma);
              }
          ?>
        </select>
      </div>
      
      <?php if ((isset($_POST['programa'])) || (isset($_POST['asignatura']))) : ?>
      <div class="form-group input-group" style="width:100%">
        <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-list-alt"></span></span>
        <select name="asignatura" id="asignatura" class="form-control" title="Asignatura" onChange="this.form.submit();" <?php if (isset($_POST['asignatura'])) {echo "disabled";} ?>>
          <option disabled value="" <?php if ((isset($_POST['programa'])) || (isset($_POST['asignatura']))) {echo "";} else {echo "selected";}?>>Asignatura</option>
          
		  <?php if ($totalRows_rsAsignatura == 0) : // Show if recordset empty ?>
          <option disabled selected value="" selected>NO HAY ASIGNATURAS CREADAS PARA ESTE PROGRAMA</option>
          <?php endif; // Show if recordset empty ?>
          
		  <?php if ($totalRows_rsAsignatura > 0) : // Show if recordset No empty ?>
          <?php
          do {  
          ?>
          <option value="<?php if (isset($_POST['asignatura'])) {echo $_POST['asignatura'];} else {echo $row_rsAsignatura['asigCodigo'];} ?>"
		  
		  <?php if (!(strcmp($row_rsSeccion['asigCodigo'], $row_rsAsignatura['asigCodigo']))) {echo "selected";} ?>
		  <?php if (isset($_POST['asignatura']))
			  {
			  if(((!(strcmp($row_rsSeccion['asigCodigo'], $row_rsAsignatura['asigCodigo']))) && ($totalRows_rsSeccion == 0)) || (!(strcmp($row_rsSeccion['asigCodigo'], $row_rsAsignatura['asigCodigo'])))) {
				  echo "selected";
				} 
			  }
			  elseif (isset($_POST['programa']))
			  {
			  if(((!(strcmp($row_rsAsignatura['progCodigo'], $row_rsPrograma['progCodigo']))) && ($totalRows_rsAsignatura == 0)) || (!(strcmp($row_rsAsignatura['progCodigo'], $row_rsPrograma['progCodigo'])))){
				  echo "selected";
				}
			  }
		  ?>
		  <?php if (isset($_POST['asignatura'])) {echo "disabled";} ?> ><?php echo $row_rsAsignatura['asigCodigo']?> <?php echo $row_rsAsignatura['asigNombre']?></option>
          <?php
            } while ($row_rsAsignatura = mysql_fetch_assoc($rsAsignatura));
              $rows = mysql_num_rows($rsAsignatura);
              if($rows > 0) {
                  mysql_data_seek($rsAsignatura, 0);
                  $row_rsAsignatura = mysql_fetch_assoc($rsAsignatura);
              }
          ?>
          <?php endif; // Show if recordset No empty ?>
        </select>
      </div>
      
      <?php if (isset($_POST['asignatura'])) : ?>
      <div class="form-group input-group" style="width:100%">
        <span class="input-group-addon" style="width:90px"><span class="glyphicon glyphicon-th-large"></span></span>
        <select name="seccion" id="seccion" class="form-control" title="Seccion" required>
          <option disabled value="">Seccion</option>
          <?php if ($totalRows_rsSeccion == 0) : // Show if recordset empty ?>
          <option disabled selected value="" selected>NO HAY SECCIONES CREADAS PARA ESTA ASIGNATURA</option>
          <?php endif; // Show if recordset empty ?>
          <?php if ($totalRows_rsSeccion > 0) : // Show if recordset No empty ?>
		  <?php
          do {  
          ?>
          <option value="<?php echo $row_rsSeccion['seccCodigo']?>"><?php echo $row_rsSeccion['seccCodigo']?> <?php echo $row_rsSeccion['espeNombre']?></option>
                         
		  <?php
            } while ($row_rsSeccion = mysql_fetch_assoc($rsSeccion));
              $rows = mysql_num_rows($rsSeccion);
              if($rows > 0) {
                  mysql_data_seek($rsSeccion, 0);
                  $row_rsSeccion = mysql_fetch_assoc($rsSeccion);
              }
          ?>
          <?php endif; // Show if recordset no empty ?>
        </select>
      </div>
<?php /////////////////// HASTA AQUI ///////////////// ?>       
    </div>
    <!--footer-->
    <?php if (isset($_POST['programa'])) : ?>
    <div class="panel-footer">  
      <input type="hidden" name="programa" value="<?php if ((isset($_POST['programa'])) || (isset($_POST['asignatura']))) {echo $_POST['programa'];} ?>">
    <?php if ((isset($_POST['asignatura'])) && (isset($_POST['asignatura']))) : ?>  
      <input type="hidden" name="asignatura" value="<?php if (isset($_POST['asignatura'])) {echo $_POST['asignatura'];} ?>">
      <input type="hidden" name="find" value="<?php if ($_SERVER['QUERY_STRING'] == "addprac") : ?>addprac<?php endif; ?><?php if ($_SERVER['QUERY_STRING'] == "editprac") : ?>editprac<?php endif; ?><?php if ($_SERVER['QUERY_STRING'] == "delprac") : ?>delprac<?php endif; ?>">
      
    <?php if ((isset($_POST['programa'])) && (isset($_POST['asignatura']))) : ?>  
    <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin">Buscar</button>
    </div>
  </div>
</form>
	<?php endif; ?>
    <?php endif; ?>    
    <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>

</div>
</div>
<?php if ((isset($_POST['asignatura'])) && (isset($_POST['seccion']))) : ?>  
<?php if ($totalRows_rsSeccion > 0) : // Show if recordset no empty ?>
<div class="container col-md-6 col-md-offset-3">
  <a href="#form" data-toggle="collapse" title="Nueva búsqueda"><span class="glyphicon glyphicon-search"></span> Nueva búsqueda</a>
  <h2>Secciones</h2>
  <h5>(<?php echo $totalRows_rsSeccion; ?> registro<?php if ($totalRows_rsSeccion > 1) { echo "s" ;}?>)</h5>
  <h3><?php echo $row_rsSeccion['asigNombre']; ?></h3>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th></th>
          <th>Código</th>
          <th>Asignatura</th>
          <th>Especialidad</th>
          <th>Vigente</th>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr>
    <td><?php if ($_SERVER['QUERY_STRING'] == "edit") : ?>
      <a href="seccionEdit.php?seccCodigo=<?php echo $row_rsSeccion['seccCodigo']; ?>" data-toggle="tooltip" data-placement="top" title="Editar"><span class="glyphicon glyphicon-pencil"></span></a>
      <?php endif; ?>
      <?php if ($_SERVER['QUERY_STRING'] == "del") : ?>
      <a href="seccionDel.php?seccCodigo=<?php echo $row_rsSeccion['seccCodigo']; ?>" data-toggle="tooltip" data-placement="top" title="Editar"><span class="glyphicon glyphicon-remove"></span></a>
      <?php endif; ?>
    <td><?php echo $row_rsSeccion['seccCodigo']; ?></td>
    <td><?php echo $row_rsSeccion['asigNombre']; ?></td>
    <td><?php echo $row_rsSeccion['espeNombre']; ?></td>
    <td><span class="glyphicon <?php if (($row_rsSeccion['seccVigencia']) == 1) { echo "glyphicon-ok"; } else { echo "glyphicon-remove";} ?>" style="text-align:center; color:<?php if (($row_rsSeccion['seccVigencia']) == 1) { echo "#0C0"; } else { echo "#F00";} ?>"></span></td>
  </tr>
  <?php } while ($row_rsSeccion = mysql_fetch_assoc($rsSeccion)); ?>
      </tbody>
    </table>
  </div>
<?php endif; // Show if recordset no empty ?> 
<?php if ($totalRows_rsSeccion == 0) : // Show if recordset empty ?>
  <div class="container col-md-6 col-md-offset-3" style="text-align:center">
    <h3>No hay resultados de búsqueda</h3>
  </div>
<?php endif; // Show if recordset empty ?>
<?php endif; ?>
</div>
  
<?php @include("../inc_session.js.php"); ?>

<br>
<br>
<br>
<br>
<br>
<br>
</body>
</html>
<?php
mysql_free_result($rsSec);

mysql_free_result($rsPers);

mysql_free_result($rsEdit);

mysql_free_result($rsSeccion);

mysql_free_result($rsAsignatura);

mysql_free_result($rsPrograma);
?>