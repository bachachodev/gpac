<?php if ($_SESSION['MM_Username'] == NULL) {
	Header("Location:/gpac/index.php?accesscheck");
}
?>
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<?php
//session timeout

$time = $_SERVER['REQUEST_TIME'];
$timeout_duration = 600; // segundos duración sesión
$countdown = 60; // segundos aviso cierre sesión
$warning = ($timeout_duration * 1000) - ($countdown * 1000); // en milisegundos
$url_logout = "/gpac/index.php?timeout";

if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
	$_SESSION['MM_Username'] = NULL;
	$_SESSION['MM_UserGroup'] = NULL;
	$_SESSION['PrevUrl'] = NULL;
	unset($_SESSION['MM_Username']);
	unset($_SESSION['MM_UserGroup']);
	unset($_SESSION['PrevUrl']);
  	session_unset();
  	session_destroy();
  	Header("Location: $url_logout");
}

$_SESSION['LAST_ACTIVITY'] = $time;
?>