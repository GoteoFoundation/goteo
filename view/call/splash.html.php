<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'splash';

include 'view/call/prologue.html.php';


?>
	
	<img id="bgimage" src="<?php echo SRC_URL ?>/view/css/call/convocatorias-tmp.jpg" alt="background" />

	<div id="main" class="onecol">
		<ul id="list">
			<li class="item" id="description">
				<img src="<?php echo SRC_URL ?>/view/css/call/logo-iniciativa.png" alt="logo" />
				<h2 class="title">CAMPA&Ntilde;A INNOVACI&Oacute;N SOCIAL EXTREMADURA</h2>
				<h2 class="subtitle">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, </h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</li>
			<li class="item" id="numbers">
				<dl class="block long last">
					<dt>Presupuesto total de campa&ntilde;a</dt>
					<dd class="money light">30,000 <span class="euro">€</span></dd>
				</dl>
				<dl class="block long">
					<dt>Queda por repartir</dt>
					<dd class="money">20,600 <span class="euro">€</span></dd>
				</dl>
				<dl class="block last category">
					<dt>Proyectos dentro de las categor&iacute;as</dt>
					<dd>Comunicativo, <a href="#">Educativo</a>, <a href="#">Tecnolog&iacute;a</a>, <a href="#">Educaci&oacute;n</a></dd>
				</dl>
				<dl class="block selected">
					<dt>Proyectos seleccionados</dt>
					<dd>26</dd>
				</dl>
				<dl class="block processing">
					<dt>Proyectos en proceso</dt>
					<dd>18</dd>
				</dl>
				<dl class="block success">
					<dt>Proyectos exitosos</dt>
					<dd>6</dd>
				</dl>
				<dl class="block last return">
					<dt>Proyectos con retornos</dt>
					<dd>
						<ul>
							<li class="product activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Instalación premium para una campaña concreta</a>
							</li>

							<li class="manual activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Acceso y utilización libre de la aplicación web</a>
							</li>
							<li class="code activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">El código de Twittometro</a>
							</li>
							<li class="service activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Asesoría para futuros administradores de la aplicación</a>
							</li>
						</ul>
						<ul>
							<li class="product activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Instalación premium para una campaña concreta</a>
							</li>

							<li class="manual activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Acceso y utilización libre de la aplicación web</a>
							</li>
							<li class="code activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">El código de Twittometro</a>
							</li>
							<li class="service activable">
								<a class="tipsy" href="#" title="Lorem ipsum dolor sit amet">Asesoría para futuros administradores de la aplicación</a>
							</li>
						</ul>
					</dd>
				</dl>
				<dl class="block location">
					<dt>Campa&ntilde;a solo para proyectos en:</dt>
					<dd>Barcelona y Cataluña, SP</dd>
				</dl>
				<a href="#" class="button aqua info">M&aacute;s info...</a>
				<a href="#" class="button red view">Ver proyectos seleccionados</a>				
			</li>
		</ul>
    </div>

	<a href="#" id="capital">Capital Riego</a>
    
<?php include 'view/epilogue.html.php' ?>