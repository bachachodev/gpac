<!--navbar-->
<style>
  /* Note: Try to remove the following lines to see the effect of CSS positioning */
  .affix {
      top: 0;
      width: 100%;
  }

  .affix + .container-fluid {
      padding-top: 70px;
  }
</style>
<!--header-->
<div class="container-fluid; img-responsive" style="color:#fff; height:80px;">
	<span class="container-fluid; img-responsive" style="color:#fff;">
    	<img src="/gpac/images/DEP_h80.png">
    </span>
</div>

<!--nav-->
<?php
//Nombre archivo
$nombre_archivo = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if ( strpos($nombre_archivo, '/') !== FALSE )
    $nombre_archivo = array_pop(explode('/', $nombre_archivo));

$url_home = "/gpac/home.php";
$url_practicas = "/gpac/practicas.php";
$url_reportes = "/gpac/reportes.php";
?>

<nav class="navbar navbar-default info" data-spy="affix" data-offset-top="80" style="z-index:10;">
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

<!--nav left-->

    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li <?php if ($nombre_archivo == $url_home):?>class="active"<?php endif;?>>
        	<a href="<?php echo $url_home ?>">		
        	<span class="glyphicon glyphicon-home"></span> Inicio
            </a>
        </li>
        
        <li class="dropdown <?php if ($nombre_archivo == $url_practicas):?>active<?php endif;?>">
        	<a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $url_practicas ?>">
            <span class="glyphicon glyphicon-pencil"></span> Gestión Prácticas
        	<span class="caret"></span>
            </a>
        	<ul class="dropdown-menu">
            	<li><a href="/gpac/adm/practicaAsign.php?agregar" tabindex="0"><span class="pull-left glyphicon glyphicon-time" style="color:orange"></span>&nbsp;&nbsp;Asignar Práctica</a></li>
                <li class="divider"></li>
          		<li><a href="/gpac/adm/practicaSeccionFind.php?add" tabindex="0"><span class="pull-left glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Crear Práctica</a></li>
          		<li><a href="/gpac/adm/practicaSeccionFind.php?edit" tabindex="0"><span class="pull-left glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Editar Práctica</a></li>
          		<li><a href="/gpac/adm/practicaSeccionFind.php?del" tabindex="0"><span class="pull-left glyphicon glyphicon-minus"></span>&nbsp;&nbsp;Eliminar Práctica</a></li>
        	</ul>
        </li>
        
        <li <?php if ($nombre_archivo == $url_reportes):?>class="active"<?php endif;?>>
        	<a href="<?php echo $url_reportes ?>">
            <span class="glyphicon glyphicon-stats"></span> Reportes
            </a>
		</li>
	</ul>
	  
<!--nav right-->

	<ul class="nav navbar-nav navbar-right">

	<!--configuración-->      
	<?php if ($row_rsSec['acceNivel']>=5) : //Gestor+ ?>
	<li class="dropdown">
		<a class="dropdown-toggle" tabindex="0" data-toggle="dropdown" data-submenu>
        <span class="glyphicon glyphicon-cog"></span> Configuración
        <span class="caret"></span>
		</a>

        <ul class="dropdown-menu">
        
		<!--agregar-->
		<li class="dropdown-submenu">
			<a tabindex="0">
            <span class="pull-right hidden-xs showopacity glyphicon glyphicon-plus"></span> Agregar
            </a>
            
			<ul class="dropdown-menu">
				<?php if ($row_rsSec['acceNivel']>=10) : //Administrador ?>
				<li>
                	<a href="/gpac/adm/personaFind.php?adduser" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-briefcase" style="color:orange"></span> Usuario
                    </a>
                </li>
				<?php endif; ?>
				<li>
                	<a href="/gpac/adm/personaAdd.php?agregar"  tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-user"></span> Persona
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                	<a href="/gpac/adm/institucionAdd.php?agregar" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-folder-open"></span> Institución
                    </a>
				</li>
                <li class="divider"></li>
                <li>
                	<a href="/gpac/adm/programaAdd.php?agregar" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-book"></span> Programa
                    </a>
                </li>
                <li>
                	<a href="/gpac/adm/asignaturaAdd.php?agregar" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-list-alt"></span> Asignatura
                    </a>
                </li>
                <li>
                	<a href="/gpac/adm/seccionAdd.php?agregar" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-th-large"></span> Sección
                    </a>
                </li>
                <li>
                	<a href="/gpac/adm/especialidadAdd.php?agregar" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-star"></span> Especialidad
                    </a>
				</li>
                <li>
                	<a href="/gpac/adm/periodoAdd.php?agregar" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-calendar"></span> Periodo
                    </a>
				</li>
			</ul>
		</li>

		<!--editar-->
		<li class="dropdown-submenu">
			<a tabindex="0">
            <span class="pull-right hidden-xs showopacity glyphicon glyphicon-pencil"></span> Editar
            </a>
            
			<ul class="dropdown-menu">
				<?php if ($row_rsSec['acceNivel']>=10) : //Administrador ?>
				<li>
                	<a href="/gpac/adm/personaFind.php?edituser" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-briefcase" style="color:orange"></span> Usuario
                    </a>
				</li>
				<?php endif; ?>                
				<li>
                	<a href="/gpac/adm/personaFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-user"></span> Persona
                    </a>
				</li>
                <li class="divider"></li>
                <li>
                	<a href="/gpac/adm/institucionFind.php?edit" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-folder-open"></span> Institución
                    </a>
				</li>
                <li class="divider"></li>
				<li>
                	<a href="/gpac/adm/programaFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-book"></span> Programa
                    </a>
				</li>
				<li>
                	<a href="/gpac/adm/asignaturaFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-list-alt"></span> Asignatura
                    </a>
				</li>
                <li>
                	<a href="/gpac/adm/seccionFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-th-large"></span> Sección
                    </a>
                </li>
				<li>
                	<a href="/gpac/adm/especialidadFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-star"></span> Especialidad
                    </a>
				</li>
                <li>
                	<a href="/gpac/adm/periodoFind.php?edit" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-calendar"></span> Periodo
                    </a>
				</li>
			</ul>
		</li>

		<!--eliminar-->
		<?php if ($row_rsSec['acceNivel']>=7) : //Coordinador+ ?>             
		<li class="dropdown-submenu">
			<a tabindex="0">
			<span class="pull-right hidden-xs showopacity glyphicon glyphicon-minus"></span> Eliminar
			</a>
            
			<ul class="dropdown-menu">
				<?php if ($row_rsSec['acceNivel']>=10) : //Administrador ?>
				<li>
                	<a href="/gpac/adm/personaFind.php?deluser" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-briefcase" style="color:orange"></span> Usuario
                    </a>
				</li>
				<?php endif; ?>    
				<li>
                	<a href="/gpac/adm/personaFind.php?del" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-user"></span> Persona
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                	<a href="/gpac/adm/institucionFind.php?del" tabindex="0">
                  	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-folder-open"></span> Institución
                    </a>
				</li>
                <li class="divider"></li>
				<li>
                	<a href="/gpac/adm/programaFind.php?del" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-book"></span> Programa
                    </a>
				</li>
				<li>
                	<a href="/gpac/adm/asignaturaFind.php?del" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-list-alt"></span> Asignatura
                    </a>
				</li>
                <li>
                	<a href="/gpac/adm/seccionFind.php?del" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-th-large"></span> Sección
                    </a>
                </li>
				<li>
                	<a href="/gpac/adm/especialidadFind.php?del" tabindex="0">
					<span class="pull-right hidden-xs showopacity glyphicon glyphicon-star"></span> Especialidad
                    </a>
				</li>
                <li>
                	<a href="/gpac/adm/periodoFind.php?del" tabindex="0">
                    <span class="pull-right hidden-xs showopacity glyphicon glyphicon-calendar"></span> Periodo
                    </a>
				</li>
			</ul>
		</li>
		<?php endif; ?>
            
		</ul>
		<?php endif; ?>
          
	</li>

	<!--usuario-->
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown">
		<span class="glyphicon glyphicon-user"></span> <?php echo ($row_rsPers['persNombres']." ".$row_rsPers['persPaterno']); ?> (<?php echo ($row_rsSec['acceDescripcion']);?>)
		<span class="caret"></span>
		</a>

		<ul class="dropdown-menu">
		<li>
        	<a href="/gpac/adm/usuarioEdit.php?numDoc=<?php echo ($row_rsPers['persNumeroDocumento']);?>&pwd" tabindex="0">
           	<span class="pull-right hidden-xs showopacity glyphicon glyphicon-pencil"></span> Contraseña 
            </a>
        </li>
                    
        <li class="divider"></li>
            
        <li>
        	<a href="<?php echo $logoutAction ?>">
            <span class="pull-right hidden-xs showopacity glyphicon glyphicon-off"></span> Salir
            </a>
        </li>
	</li>
	</ul>
        
    </div>
	</div>
</nav>

<!--end nav-->
<!--////////////////////////////////-->
