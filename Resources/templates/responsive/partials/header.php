<!-- Fixed navbar -->
    <nav class="navbar main-color-background">
      <div class="container">
       <div class="pull-left">
          <a href="/"><img src="<?= SRC_URL ?>/assets/img/logo.png" class="img-responsive logo" alt="Goteo"></a> 
       </div>
       <?php if (is_null($this->get_user())) : ?>
      	<div class="navbar-right">
	      <ul class="nav navbar-nav">
	        
	          <li>
	            <a class="navbar-element" href="/signup">Registrarse</a>
	          </li>
	        
	          <li>
	            <a class="navbar-element" href="/login">Iniciar sesión</a>
	          </li>
	        
	      </ul>
    	</div>
    	<?php else: ?>
    	<div class="pull-right">
    		<a href="/dashboard"><span></span><img class="avatar-radius" src="<?= $this->get_user()->avatar->getLink(35, 35, true); ?>" /></a>
    	<?php endif; ?>
      </div>
    </nav>

