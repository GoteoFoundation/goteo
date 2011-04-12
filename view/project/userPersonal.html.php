<?php 

use Goteo\Library\Text;

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

            <?php include 'view/project/status.html.php' ?>
            <?php include 'view/project/steps.html.php' ?>

            <div class="superform">

                <h3>Usuario/Datos personales</h3>

                <?php include 'view/project/guide.html.php' ?>                                								

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

                <div class="buttons">
                    <input class="next" type="submit" name="view-step-overview" value="Continuar"  />
                </div>

            </div>

            <?php include 'view/project/steps.html.php' ?>
            
            <?php include 'view/project/tooltips.js.php' ?>            

        </form>

    </div>            

    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>
