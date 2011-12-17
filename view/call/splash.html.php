<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'splash';

$call = $this['call'];

include 'view/call/prologue.html.php';
?>
	
	<img id="bgimage" src="<?php echo SRC_URL ?>/image/<?php echo $call->image ?>/2000/2000" alt="background" />

	<div id="main" class="onecol">
		<ul id="list">
			<li class="item" id="description">
				<img src="<?php echo SRC_URL ?>/image/<?php echo $call->logo ?>" alt="logo" />
                <h2 class="title">CAMPA&Ntilde;A<br /><?php echo $call->name ?></h2>
                <?php if ($call->status == 3) : //inscripcion ?>
                <h2>Se buscan proyectos!</h2>
                <?php else : //en campaña ?>
				<h2 class="subtitle">Por cada (1&euro;) que das a un proyecto en www.goteo.org, <strong><?php echo $call->user->name ?></strong> aporta otro al proyecto que has apoyado. Los proyectos participantes han sido seleccionados por convocatoria abierta.</h2>
                <?php endif; ?>
				<p><?php echo $call->description ?></p>
			</li>
			<li class="item" id="numbers">
            <?php if ($call->status == 3) : //inscripcion ?>
				<dl class="block long last">
					<dt>Presupuesto total de campa&ntilde;a</dt>
                    <dd class="money<?php if ($call->status == 4) echo ' light' ?>"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
				</dl>
				<dl class="block">
					<dt>Convocatoria válida hasta:</dt>
					<dd><span>12</span>Nov/2011</dd>
				</dl>
				<dl class="block last selected">
					<dt>Proyectos inscritos</dt>
					<dd><?php echo count($call->projects) ?></dd>
				</dl>
				<dl class="block category">
					<dt>Proyectos dentro de las categor&iacute;as</dt>
					<dd><?php echo implode(', ', $call->categories) ?></dd>
				</dl>
				<dl class="block return">
					<dt>Proyectos con retornos</dt>
					<dd>
						<ul>
                            <?php foreach ($call->icons as $iconId=>$iconName) : ?>
							<li class="<?php echo $iconId ?> activable">
								<a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
							</li>
                            <?php endforeach; ?>
						</ul>
					</dd>
				</dl>
				<dl class="block last">
					<dt>Más información</dt>
					<dd>
                        <a href="<?php echo $call->pdf ?>" target="_blank">Descarga el PDF con las bases</a><br />
                        <a href="<?php echo $call->user->webs[0]->url ?>" target="_blank"><?php echo $call->user->webs[0]->url ?></a><br />
                        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/legal" target="_blank">Términos y condiciones</a>
                    </dd>
				</dl>
            <?php else : //en campaña ?>
				<dl class="block long last">
					<dt>Presupuesto total de campa&ntilde;a</dt>
                    <dd class="money light"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
				</dl>
				<dl class="block long">
					<dt>Queda por repartir</dt>
					<dd class="money"><?php echo \amount_format($call->rest) ?> <span class="euro">&euro;</span></dd>
				</dl>
				<dl class="block last category">
					<dt>Proyectos dentro de las categor&iacute;as</dt>
					<dd><?php echo implode(', ', $call->categories) ?></dd>
				</dl>
				<dl class="block selected">
					<dt>Proyectos seleccionados</dt>
					<dd><?php echo count($call->projects) ?></dd>
				</dl>
				<dl class="block processing">
					<dt>Proyectos en proceso</dt>
					<dd><?php echo $call->runing_projects ?></dd>
				</dl>
				<dl class="block success">
					<dt>Proyectos exitosos</dt>
					<dd><?php echo $call->success_projects ?></dd>
				</dl>
				<dl class="block last return">
					<dt>Proyectos con retornos</dt>
					<dd>
						<ul>
                            <?php foreach ($call->icons as $iconId=>$iconName) : ?>
							<li class="<?php echo $iconId ?> activable">
								<a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
							</li>
                            <?php endforeach; ?>
						</ul>
					</dd>
				</dl>
            <?php endif; ?>

				<dl class="block location">
					<dt>Campa&ntilde;a solo para proyectos en:</dt>
					<dd><?php echo Text::GmapsLink($call->call_location); ?></dd>
				</dl>

				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button aqua info" target="_blank">M&aacute;s info...</a>
            <?php if ($call->status == 3) : //inscripcion ?>
				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red view" target="_blank">Participar</a>
            <?php else : // ver proyectos ?>
				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view" target="_blank">Ver proyectos seleccionados</a>
            <?php endif; ?>
			</li>
		</ul>
    </div>

	<a href="<?php echo URL_SITE ?>/service/resources" id="capital" target="_blank">Capital Riego</a>
    
<?php include 'view/epilogue.html.php' ?>