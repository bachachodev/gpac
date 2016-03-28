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
	
  $logoutGoTo = "index.php?access=logout";
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

$MM_restrictGoTo = "index.php";
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

$colname_sec = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_sec = $_SESSION['MM_Username'];
}
mysql_select_db($database_rsPP, $rsPP);
$query_sec = sprintf("SELECT * FROM `access` WHERE secUsr = %s", GetSQLValueString($colname_sec, "text"));
$sec = mysql_query($query_sec, $rsPP) or die(mysql_error());
$row_sec = mysql_fetch_assoc($sec);
$totalRows_sec = mysql_num_rows($sec);
?>

<?php @include("inc_session.php"); ?>

<!doctype html>
<html>
<head>
<title>GPAC - Inicio</title>
<?php @include("inc_assets.php"); ?>
<meta charset="utf-8">
<style type="text/css">
body {
	margin-top: 0px;
}
</style>
</head>
<body>

<!-- Header -->
<div class="container-fluid; img-responsive" style=color:#fff; height:80px;">
	<span class="container-fluid; img-responsive" style="color:#fff;">
    	<img src="images/DEP_h80.png">
    </span></td>
</div>

<?php
//Nombre archivo
$nombre_archivo = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if ( strpos($nombre_archivo, '/') !== FALSE )
    $nombre_archivo = array_pop(explode('/', $nombre_archivo));
?>

<!-- NavBar -->
<?php
$url_home = "home.php";
$url_busqueda = "busqueda.php";
$url_reportes = "reportes.php";
?>

<nav class="navbar navbar-default" data-spy="affix" data-offset-top="80" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
      <a class="navbar-brand">Prácticas Académicas</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li <?php if ($nombre_archivo == $url_home):?>class="active"<?php endif;?>><a href="<?php echo $url_home ?>"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
        <li <?php if ($nombre_archivo == $url_busqueda):?>class="active"<?php endif;?>><a href="<?php echo $url_busqueda ?>"><span class="glyphicon glyphicon-search"></span> Búsqueda</a></li>
        <li <?php if ($nombre_archivo == $url_reportes):?>class="active"<?php endif;?>><a href="<?php echo $url_reportes ?>"><span class="glyphicon glyphicon-stats"></span> Reportes</a></li>
      </ul>
	  
<!-- NavBar Right-->
<!-- Header-->
	  <ul class="nav navbar-nav navbar-right">

<!-- Configuración-->      
<?php if ($row_sec['secNivel']>=5) : //Asist+ ?>
      <li class="dropdown">
         <a tabindex="0" data-toggle="dropdown" data-submenu>
         <span class="glyphicon glyphicon-cog"></span> Configuración<span class="caret"></span></a>

         <ul class="dropdown-menu">

	<!-- Add-->
			<li class="dropdown-submenu">
                <a tabindex="0"><span class="glyphicon glyphicon-plus"></span> Agregar</a>
                    <ul class="dropdown-menu">
						<?php if ($row_sec['secNivel']==10) : //Admin ?>
						<li><a href="adm/usuario.php?action=add" tabindex="0">
							<span class="glyphicon glyphicon-briefcase"></span> Usuario</a></li>
						<?php endif; ?>    
                        <li><a href="adm/personaAdd.php?action=add"  tabindex="0">
                        	<span class="glyphicon glyphicon-user"></span> Persona</a></li>
                        <li><a href="adm/programa.php?action=add" tabindex="0">
                        	<span class="glyphicon glyphicon-book"></span> Programa</a></li>
                        <li><a href="adm/asignatura.php?action=add" tabindex="0">
                        	<span class="glyphicon glyphicon-list-alt"></span> Asignatura</a></li>
                        <li><a href="adm/especialidad.php?action=add" tabindex="0">
                        	<span class="glyphicon glyphicon-star"></span> Especialidad</a></li>
                    </ul>
			</li>

	<!-- Edit-->
			<li class="dropdown-submenu">
                <a tabindex="0"><span class="glyphicon glyphicon-pencil"></span> Editar</a>
                    <ul class="dropdown-menu">
                        <?php if ($row_sec['secNivel']==10) : //Admin ?>
                        <li><a href="adm/usuario.php?action=edit" tabindex="0">
                       	<span class="glyphicon glyphicon-briefcase"></span> Usuario</a></li>
                        <?php endif; ?>
                        <li><a href="adm/personaAdd.php?action=edit"  tabindex="0">
                       	<span class="glyphicon glyphicon-user"></span> Persona</a></li>
                        <li><a href="adm/programa.php?action=edit" tabindex="0">
                       	<span class="glyphicon glyphicon-book"></span> Programa</a></li>
                        <li><a href="adm/asignatura.php?action=edit" tabindex="0">
                       	<span class="glyphicon glyphicon-list-alt"></span> Asignatura</a></li>
                        <li><a href="adm/especialidad.php?action=edit" tabindex="0">
                       	<span class="glyphicon glyphicon-star"></span> Especialidad</a></li>
                    </ul>
			</li>

	<!-- Delete-->
			<?php if ($row_sec['secNivel']>=7) : //Coord+ ?>             
            <li class="dropdown-submenu">
            	<a tabindex="0"><span class="glyphicon glyphicon-minus"></span> Eliminar</a>
                    <ul class="dropdown-menu">
                    	<?php if ($row_sec['secNivel']==10) : //Admin ?>
                        <li><a href="adm/usuario.php?action=del" tabindex="0">
                       	<span class="glyphicon glyphicon-briefcase"></span> Usuario</a></li>
                        <?php endif; ?>    
                        <li><a href="adm/personaAdd.php?action=del"  tabindex="0">
                       	<span class="glyphicon glyphicon-user"></span> Persona</a></li>
                        <li><a href="adm/programa.php?action=del" tabindex="0">
                       	<span class="glyphicon glyphicon-book"></span> Programa</a></li>
                        <li><a href="adm/asignatura.php?action=del" tabindex="0">
                       	<span class="glyphicon glyphicon-list-alt"></span> Asignatura</a></li>
                        <li><a href="adm/especialidad.php?action=del" tabindex="0">
                       	<span class="glyphicon glyphicon-star"></span> Especialidad</a></li>
                    </ul>
			</li>
			<?php endif; ?>
            
		</ul>
<?php endif; ?>
          
</li>

<!-- User-->
	<li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown">
		<span class="glyphicon glyphicon-user"></span> <?php echo $row_sec['secDescripcion'];?>
            <span class="caret"></span></a>

			<ul class="dropdown-menu">
                <li><a href="adm/usuario.php?action=edit" tabindex="0">
                    <span class="glyphicon glyphicon-pencil"></span> Editar Usuario</a></li>
            <li class="divider"></li>
                <li><a href="<?php echo $logoutAction ?>">
                    <span class="glyphicon glyphicon-off"></span> Logout</a></li>
		    </ul>
    </div>
  </div>
</nav>

<!-- Header -->

<div class="row"></div>
</body>
</html>
<?php
mysql_free_result($sec);
?>