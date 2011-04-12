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

                    <h3>PROYECTO / Previsualización</h3>

                    <?php include 'view/project/guide.html.php' ?>

                    <?php //@INTRUSION JULIAN!!! para usarlo sin maquetación
                    if ($this['nodesign'] == true) : ?>
                    Comentario:<br />
                    <textarea name="comment" rows="20" cols="100"><?php echo $this['project']->comment; ?></textarea>
                    <?php else : ?>
            <?php if (!empty($project->errors['costs'])) :
                echo '<p>';
                foreach ($project->errors['costs'] as $campo=>$error) : ?>
                    <span style="color:red;"><?php echo "$campo: $error"; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>
                    <pre><?php echo print_r($project, 1) ?></pre>

                    <?php endif; ?>
                    <div class="buttons">
                        <input type="hidden" name="step" value="preview" />
                        <input type="submit" value="Continuar" name="view-step-preview" class="next" />
                    </div>

                </div>

                <?php include 'view/project/steps.html.php' ?>
                <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>