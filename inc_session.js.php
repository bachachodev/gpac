
<!-- Timeout -->
<script>
var c = 0;
var max_count = <?php echo($countdown); ?>;
logout = true;
startTimer();

function startTimer(){
	setTimeout(function(){
		logout = true;
		c = 0; 
		max_count = <?php // echo($countdown); ?>;
		$('#timer').html(max_count);
		$('#logout_popup').modal('show');
		timedCount();

	}, <?php echo($warning); ?>);
}

<!-- Tiempo restante -->
function timedCount() {
	c = Math.floor (c + 1);
   	remaining_time = max_count - c;
   	if( remaining_time == 0 && logout ){
   		$('#logout_popup').modal('hide');
		location.href="<?php echo($url_logout); ?>"; 
	}
	else{
    	$('#timer').html(remaining_time);
    	t = setTimeout(function(){timedCount()}, 1000);
	}
}

//-- Reset
function resetTimer(){
	c = 0;
	logout = false;
	$('#logout_popup').modal('hide');
	startTimer();
}

function Keypress(event) {
    Key = event.keyCode;
		if(Key==27){ //escape
			resetTimer()
    	}
}
 
window.onkeydown=Keypress;

</script>

<div class="modal fade" id="logout_popup" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" align="center">Su sesión cerrará pronto</h3>
            </div>
			<div class="modal-body">
				<div style="width:100%;height:50%;margin: 0px; padding:0px">
					<div style="width:50%;margin: 0px; padding:0px;float:none;">
						<i class="fa fa-warning" style="font-size: 140px;color:#da4f49"></i>
					</div>
					<div style="width:98%;margin: 0px; padding:0px;float:right;padding-top: 0px;padding-left: 0%;">
                    	<p style="font-size: 12px;">Las sesiones expiran transcurridos <?php echo($timeout_duration / 60) ?> minutos de inactividad. Haga clic en Aceptar para proseguir con la sesión que tiene abierta.</p>
						<p style="font-size: 15px;" align="center">Esta sesión cerrará en <br><span id="timer" style="display: inline;font-size: 30px;font-style: bold"><?php echo($countdown); ?></span> segundos.</p>
					</div>
				</div>
			</div>
			<div style="margin-left:0%; margin-bottom:20px; margin-top:20px;" align="center">
				<a href="#" onclick="resetTimer()" class="btn btn-info" role="button">ACEPTAR</a>
			</div>
		</div>
	</div>
</div>
