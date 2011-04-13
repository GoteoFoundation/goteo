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

        <div id="main" class="costs">

            <form method="post" action="">

                <?php echo new View('view/project/status.html.php', array('status' => $this['project']->status, 'progress' => $this['project']->progress)) ?>
                <?php echo new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'])) ?>

                <div class="superform red">

                    <h3><?php echo $this['title']; ?></h3>

                    <?php echo new View('view/project/guide.html.php', array('text' => $this['steps'][$this['step']]['guide'])) ?>

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

                    <?php endif; ?>
                    <div class="buttons">
                        <input type="hidden" name="step" value="preview" /><!-- por ahora no me escapo de tener que poner esto... -->
                        <input type="submit" value="Continuar" name="view-step-preview" class="next" />
                    </div>

                </div>

            <?php echo new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'])) ?>

            <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>