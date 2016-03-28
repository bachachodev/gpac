<?php
$OS = strtoupper(substr(PHP_OS, 0, 3));
//if {$OS == "win";
//ECHO $OS;
//ELSE {
//ECHO "ERROR";}
//end if};
echo PHP_VERSION;
echo "<br><br>0 ================ <br><br>";
$DIR = $_SERVER['DOCUMENT_ROOT'];
$DIR = $DIR.'/db';
ECHO $DIR;
echo "<br><br>1 ================ <br><br>";
$A = $DIR;  
#$B = strrev ($A);  
$C = strstr ($A ,'\\');  
$D = strrev ($C); 
ECHO $D; 
echo "<br><br>2 ================ <br><br>";

$indicesServer = array('PHP_SELF', 
'PHP_VERSION',
'argv', 
'argc', 
'GATEWAY_INTERFACE', 
'SERVER_ADDR', 
'SERVER_NAME', 
'SERVER_SOFTWARE', 
'SERVER_PROTOCOL', 
'REQUEST_METHOD', 
'REQUEST_TIME', 
'REQUEST_TIME_FLOAT', 
'QUERY_STRING', 
'DOCUMENT_ROOT', 
'HTTP_ACCEPT', 
'HTTP_ACCEPT_CHARSET', 
'HTTP_ACCEPT_ENCODING', 
'HTTP_ACCEPT_LANGUAGE', 
'HTTP_CONNECTION', 
'HTTP_HOST', 
'HTTP_REFERER', 
'HTTP_USER_AGENT', 
'HTTPS', 
'REMOTE_ADDR', 
'REMOTE_HOST', 
'REMOTE_PORT', 
'REMOTE_USER', 
'REDIRECT_REMOTE_USER', 
'SCRIPT_FILENAME', 
'SERVER_ADMIN', 
'SERVER_PORT', 
'SERVER_SIGNATURE', 
'PATH_TRANSLATED', 
'SCRIPT_NAME', 
'REQUEST_URI', 
'PHP_AUTH_DIGEST', 
'PHP_AUTH_USER', 
'PHP_AUTH_PW', 
'AUTH_TYPE', 
'PATH_INFO', 
'ORIG_PATH_INFO') ; 

echo '<table cellpadding="10">' ; 
foreach ($indicesServer as $arg) { 
    if (isset($_SERVER[$arg])) { 
        echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ; 
    } 
    else { 
        echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ; 
    } 
} 
echo '</table>' ;

echo "<br><br>================ <br><br>";
echo php_uname();
echo PHP_OS;

echo "<br><br>================ <br><br>";

   foreach ($_SERVER as $key=>$value) 
   {
      echo $key."=".$value;
      echo "<br><br>";
   }
   
echo substr($_SERVER['QUERY_STRING'], 0, 11);
?>