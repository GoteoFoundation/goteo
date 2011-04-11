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

        <div id="main" class="costs">

            <form method="post" action="">

                <?php include 'view/project/status.html.php' ?>
                <?php include 'view/project/steps.html.php' ?>

                <div class="superform red">

                    <h3>Proyecto/Costes</h3>

                    <?php include 'view/project/guide.html.php' ?>

                    <?php //@INTRUSION JULIAN!!! para usarlo sin maquetación
                    if ($this['nodesign'] == true) :
                        $project = $this['project'];
                        $types = $this['types'];
                        ?>
            <?php if (!empty($project->errors['costs'])) :
                echo '<p>';
                foreach ($project->errors['costs'] as $campo=>$error) : ?>
                    <span style="color:red;"><?php echo "$campo: $error"; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>
                        <fieldset>
                            <legend>Nuevo coste</legend>
                            <dl>
                                <dt><label for="ncost">Coste</label></dt>
                                <dd><input type="text" id="ncost" name="ncost" value=""/></dd>

                                <dt><label for="ncost-description">Descripción</label></dt>
                                <dd><textarea id="ncost-description" name="ncost-description" cols="20" rows="3"></textarea></dd>

                                <dt><label for="ncost-amount">Costes</label></dt>
                                <dd><input type="text" id="ncost-amount" name="ncost-amount" value="" /></dd>

                                <dt><label for="ncost-type">Tipo</label></dt>
                                <dd><select id="ncost-type" name="ncost-type">
                                        <?php foreach ($types as $Id => $Val) : ?>
                                            <option value="<?php echo $Id; ?>"><?php echo $Val; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                </dd>

                                <dt><label for="ncost-required">Imprescindible</label></dt>
                                <dd><input type="checkbox" id="ncost-required" name="ncost-required" value="1" /></dd>

                                <dt><label for="ncost-from">Desde</label></dt>
                                <dd><input type="text" id="ncost-from" name="ncost-from" value="<?php echo date('Y-m-d'); ?>" /></dd>

                                <dt><label for="ncost-until">Hasta</label></dt>
                                <dd><input type="text" id="ncost-until" name="ncost-until" value="<?php echo date('Y-m-d'); ?>" /></dd>

                            </dl>

                            <input type="button" id="new-cost" value="Nuevo coste" />

                            <span><?php echo Text::get('tooltip project ncost'); ?></span><br />
                        </fieldset>

                        <?php foreach ($project->costs as $cost) : ?>
                                <fieldset>
                                    <legend>Coste <?php echo $cost->id; ?></legend>
                                    <label>REMOVE! <input type="checkbox" name="remove-cost<?php echo $cost->id; ?>" value="1" /></label>
                                    <dl>
                                        <dt><label for="cost<?php echo $cost->id; ?>">Coste</label></dt>
                                        <dd><input type="text" id="cost<?php echo $cost->id; ?>" name="cost<?php echo $cost->id; ?>" value="<?php echo $cost->cost; ?>"/></dd>

                                        <dt><label for="cost-description<?php echo $cost->id; ?>">Descripción</label></dt>
                                        <dd><textarea id="cost-description<?php echo $cost->id; ?>" name="cost-description<?php echo $cost->id; ?>" cols="20" rows="3"><?php echo $cost->description; ?></textarea></dd>

                                        <dt><label for="cost-type<?php echo $cost->id; ?>">Tipo</label></dt>
                                        <dd><select id="cost-type<?php echo $cost->id; ?>" name="cost-type<?php echo $cost->id; ?>">
                                            <?php foreach ($types as $Id => $Val) : ?>
                                                <option value="<?php echo $Id; ?>"<?php if ($cost->type == $Id) echo ' selected="selected"'; ?>><?php echo $Val; ?></option>
                                            <?php endforeach; ?>
                                            </select>
                                        </dd>

                                    <dt><label for="cost-amount<?php echo $cost->id; ?>">Costes</label></dt>
                                    <dd><input type="text" id="cost-amount<?php echo $cost->id; ?>" name="cost-amount<?php echo $cost->id; ?>" value="<?php echo $cost->amount; ?>" /></dd>

                                    <dt><label for="cost-required<?php echo $cost->id; ?>">Imprescindible</label></dt>
                                    <dd><input type="checkbox" id="cost-required<?php echo $cost->id; ?>" name="cost-required<?php echo $cost->id; ?>" value="1" <?php if ($cost->required) echo 'checked="checked"'; ?>/></dd>

                                    <dt><label for="cost-from<?php echo $cost->id; ?>">Desde</label></dt>
                                    <dd><input type="text" id="cost-from<?php echo $cost->id; ?>" name="cost-from<?php echo $cost->id; ?>" value="<?php echo $cost->from; ?>" /></dd>

                                    <dt><label for="cost-until<?php echo $cost->id; ?>">Hasta</label></dt>
                                    <dd><input type="text" id="cost-until<?php echo $cost->id; ?>" name="cost-until<?php echo $cost->id; ?>" value="<?php echo $cost->until; ?>" /></dd>
                                </dl>

                                    <span><?php echo Text::get('tooltip project cost'); ?></span><br />
                                </fieldset>
                        <?php endforeach; ?>

                        <fieldset>
                            <legend>Coste total del proyecto</legend>
                            <p>Mínimo: <?php echo $project->mincost; ?> &euro; | Óptimo: <?php echo $project->maxcost; ?> &euro;</p>
                        </fieldset>

                        <fieldset>
                            <legend>Cuenta con otros recursos?</legend>
                            <dl>
                                <dt><label for="resource">Otras ayudas económicas o infraestructura</label></dt>
                                <dd><textarea id="resource" name="resource" cols="50" rows="5"><?php echo $project->resource; ?></textarea></dd>
                            </dl>
                            <span><?php echo Text::get('tooltip project resource'); ?></span><br />
                        </fieldset>

                        <fieldset>
                            <legend>AGENDA</legend>
                            Tiempo de producción del proyecto<br />
                            <?php foreach ($project->costs as $cost) : ?>
                                <p><?php echo "{$cost->cost}. Del {$cost->from} al {$cost->until}"; ?></p>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php else :  // si es con maquetación?>

                    <div class="fields">

                        <ol class="fields">

                            <li class="field" id="field-cost">
                                
                                <div class="field">
                                
                                    <h4>Desglose de costes</h4>

                                    <div class="tooltip" id="tooltip-cost">
                                        <blockquote><?php echo Text::get('tooltip project cost'); ?></blockquote>
                                    </div>

                                    <div class="elements">

                                        <ol class="costs">    

                                            <?php foreach ($project->costs as $cost): ?>                                        
                                            <li>

                                                <div class="description">
                                                    <label>Descripción:
                                                    <textarea><?php echo htmlspecialchars($cost->description) ?></textarea></label>
                                                </div>

                                                <div class="value">
                                                    <label>Valor:
                                                    <input type="text" /></label>
                                                </div>

                                                <div class="required">
                                                    <label><input type="checkbox" value="1" />
                                                    Imprescindible</label>                                            
                                                </div>

                                                <div class="since">
                                                    <label>Desde
                                                    <input type="text" /></label>                                            
                                                </div>

                                                <div class="through">
                                                    <label>Hasta
                                                    <input type="text" /></label>                                            
                                                </div>

                                            </li>
                                            <?php endforeach ?>

                                        </ol>

                                        <div class="addcost">                                                                            
                                            <input type="submit" value="Añadir" class="add" />                                                                                
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </li>

                            <li class="field" id="field-resource">      
                                <div class="field">
                                    <h4>¿Cuenta con otros recursos?</h4>
                                    <div class="tooltip" id="tooltip-resource">
                                        <blockquote><?php echo Text::get('tooltip project resource'); ?></blockquote>
                                    </div>
                                    <div class="elements">
                                        <textarea></textarea>                        
                                    </div>
                                </div>
                            </li>

                            <li class="schedule">
                                <div class="field">
                                    <h4>Agenda</h4>    
                                </div>
                            </li>

                        </ol>
                        
                    </div>
                    <?php endif; //@INTRUSION JULIAN FIN ?>

                    <div class="buttons">
                        <input type="hidden" name="step" value="costs" />
                        <input type="submit" name="view-step-rewards" value="Continuar" class="next" />
                    </div>

                </div>

                <?php include 'view/project/steps.html.php' ?>
                <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>
                
    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>