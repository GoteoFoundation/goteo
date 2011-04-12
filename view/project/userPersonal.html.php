<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'project-form';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Formulario</h2>
            </div>
        </div>

    <div id="main" class="userProfile">

        <form method="post" action="" class="project">

            <?php echo new View('view/project/status.html.php', array('status' => $this['project']->status, 'progress' => $this['project']->progress)) ?>
            <?php echo new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'])) ?>

            <div class="superform">

                    <h3><?php echo $this['title']; ?></h3>

                    <?php echo new View('view/project/guide.html.php', array('text' => $this['steps'][$this['step']]['guide'])) ?>

                    <?php //@INTRUSION JULIAN!!! para usarlo sin maquetación
                    if ($this['nodesign'] == true) :
                        $project = $this['project'];
                        ?>
            <?php if (!empty($project->errors['userPersonal'])) :
                echo '<p>';
                foreach ($project->errors['userPersonal'] as $campo=>$error) : ?>
                    <span style="color:red;"><?php echo "$campo: $error"; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>
	<dl>
		<dt><label for="contract_name">Nombre</label></dt>
		<dd><input type="text" id="contract_name" name="contract_name" value="<?php echo $project->contract_name; ?>"/></dd>
		<span><?php echo Text::get('tooltip project contract_name'); ?></span><br />

		<dt><label for="contract_surname">Apellidos</label></dt>
		<dd><input type="text" id="contract_surname" name="contract_surname" value="<?php echo $project->contract_surname; ?>"/></dd>
		<span><?php echo Text::get('tooltip project contract_surname'); ?></span><br />

		<dt><label for="contract_nif">NIF</label></dt>
		<dd><input type="text" id="contract_nif" name="contract_nif" value="<?php echo $project->contract_nif; ?>"/></dd>
		<span><?php echo Text::get('tooltip project contract_nif'); ?></span><br />

		<dt><label for="contract_email">Email</label></dt>
		<dd><input type="text" id="contract_email" name="contract_email" value="<?php echo $project->contract_email; ?>"/></dd>
		<span><?php echo Text::get('tooltip project contract_email'); ?></span><br />

		<dt><label for="phone">Teléfono</label></dt>
		<dd><input type="text" id="phone" name="phone" value="<?php echo $project->phone; ?>"/></dd>
		<span><?php echo Text::get('tooltip project phone'); ?></span><br />

		<dt><label for="address">Dirección</label></dt>
		<dd><input type="text" id="address" name="address" value="<?php echo $project->address; ?>"/></dd>
		<span><?php echo Text::get('tooltip project address'); ?></span><br />

		<dt><label for="zipcode">Código postal</label></dt>
		<dd><input type="text" id="zipcode" name="zipcode" value="<?php echo $project->zipcode; ?>"/></dd>
		<span><?php echo Text::get('tooltip project zipcode'); ?></span><br />

		<dt><label for="location">Lugar de residencia</label></dt>
		<dd><input type="text" id="location" name="location" value="<?php echo $project->location; ?>"/></dd>
		<span><?php echo Text::get('tooltip project location'); ?></span><br />

		<dt><label for="country">País</label></dt>
		<dd><input type="text" id="country" name="country" value="<?php echo $project->country; ?>"/></dd>
		<span><?php echo Text::get('tooltip project country'); ?></span><br />

	</dl>
                    <?php else : ?>
                <div class="fields">
                    
                    <ol class="fields">

                        <li class="field" id="field-contract_name">
                            <div class="field">
                                <h4>Nombre</h4>
                                <div class="tooltip" id="tooltip-contract_name">
                                    <blockquote><?php echo Text::get('tooltip-project-contract_name'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="contract_name" value="<?php if (isset($project->contract_name)) echo htmlspecialchars($project->contract_name) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-contract_surname">
                            <div class="field">
                                <h4>Apellidos</h4>
                                <div class="tooltip" id="tooltip-contract_surname">
                                    <blockquote><?php echo Text::get('tooltip-project-contract_surname'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="contract_surname" value="<?php if (isset($project->contract_surname)) echo htmlspecialchars($project->contract_surname) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-contract_nif">
                            <div class="field">
                                <h4>NIF</h4>
                                <div class="tooltip" id="tooltip-contract_nif">
                                    <blockquote><?php echo Text::get('tooltip-project-contract_nif'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="contract_nif" value="<?php if (isset($project->contract_nif)) echo htmlspecialchars($project->contract_nif) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-contract_email">
                            <div class="field">
                                <h4>E-mail</h4>
                                <div class="tooltip" id="tooltip-contract_email">
                                    <blockquote><?php echo Text::get('tooltip-project-contract_email'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="contract_email" value="<?php if (isset($project->contract_email)) echo htmlspecialchars($project->contract_email) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-phone">
                            <div class="field">
                                <h4>E-mail</h4>
                                <div class="tooltip" id="tooltip-phone">
                                    <blockquote><?php echo Text::get('tooltip-project-phone'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="phone" value="<?php if (isset($project->phone)) echo htmlspecialchars($project->phone) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-address">
                            <div class="field">
                                <h4>E-mail</h4>
                                <div class="tooltip" id="tooltip-address">
                                    <blockquote><?php echo Text::get('tooltip-project-address'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="address" value="<?php if (isset($project->address)) echo htmlspecialchars($project->address) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-zipcode">
                            <div class="field">
                                <h4>E-mail</h4>
                                <div class="tooltip" id="tooltip-zipcode">
                                    <blockquote><?php echo Text::get('tooltip-project-zipcode'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="zipcode" value="<?php if (isset($project->zipcode)) echo htmlspecialchars($project->zipcode) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-location">
                            <div class="field">
                                <h4>Lugar de residencia</h4>
                                <div class="tooltip" id="tooltip-location">
                                    <blockquote><?php echo Text::get('tooltip-project-location'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="location" value="<?php if (isset($project->location)) echo htmlspecialchars($project->location) ?>" /></div>                                
                            </div>
                        </li>
                        
                        <li class="field" id="field-country">
                            <div class="field">
                                <h4>País</h4>
                                <div class="tooltip" id="tooltip-country">
                                    <blockquote><?php echo Text::get('tooltip-project-country'); ?></blockquote>
                                </div>
                                <div class="elements"><input type="text" name="country" value="<?php if (isset($project->country)) echo htmlspecialchars($project->country) ?>" /></div>                                
                            </div>
                        </li>
                        
                    </ol>
                    
                </div>

                    <?php endif; ?>
                    <div class="buttons">
                        <input type="hidden" name="step" value="userPersonal" /><!-- por ahora no me escapo de tener que poner esto... -->
                        <input class="next" type="submit" name="view-step-overview" value="Continuar"  />
                    </div>

                </div>

                <?php echo new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'])) ?>

                <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>