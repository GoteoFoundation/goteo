<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'project-edit';

$contract = $this['contract'];

$steps  = new View('view/contract/edit/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'], 'errors' => $this['contract']->errors));

Goteo\Library\Message::Info(Text::get('form-ajax-info'));

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div class="project-header">
            <h2><span><?php echo htmlspecialchars($contract->project_name) ?></span></h2>
        </div>
    </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div id="main" class="<?php echo htmlspecialchars($this['step']) ?>">

        <form method="post" action="<?php echo "/contract/edit/" . $this['contract']->id ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?php echo $this['step'] ?>" value="please" />

            <?php echo $steps ?>

            <?php echo new View("view/contract/edit/{$this['step']}.html.php", $this->getArrayCopy() + array('level' => 3)) ?>

            <?php echo $steps ?>

        </form>

    </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
