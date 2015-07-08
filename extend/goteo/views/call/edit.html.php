<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'project-edit';
$vars = $this->vars;
$call = $vars['call'];

if (!empty($vars['success'])) {
    Goteo\Application\Message::info($vars['success']);
} elseif ($call->status == 1) {
    Goteo\Application\Message::info(Text::get('call-form-ajax-info'));
}

$steps  = View::get('call/edit/steps.html.php', array('steps' => $vars['steps'], 'step' => $vars['step'], 'id_call' => $call->id));

// next step
$debug = (isset($_GET['debug']));
if ($debug) var_dump($vars['step']);
$keys = array_keys($vars['steps']);
if ($debug) var_dump($keys);
$next_step = $keys[ array_search($vars['step'], $keys) + 1];
if ($debug) var_dump($next_step);
if ($debug) die;


$superform = true;

    include __DIR__ . '/../../../../app/view/prologue.html.php';

    include __DIR__ . '/../../../../app/view/header.html.php';
?>

    <div id="sub-header">
        <div class="project-header">
            <a href="/user/<?php echo $call->owner; ?>" target="_blank"><img src="<?php echo SITE_URL ?>/image/<?php echo $call->user->avatar->id; ?>/50/50/1" /></a>
            <h2><span><?php echo htmlspecialchars($call->name) ?></span></h2>
            <div class="project-subtitle"><?php echo htmlspecialchars($call->subtitle) ?></div>
            <div class="project-by"><a href="/user/<?php echo $call->owner; ?>" target="_blank">Por: <?php echo $call->user->name; ?></a></div>
        </div>
    </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../../../../app/view/header/message.html.php'; } ?>

    <div id="main" class="<?php echo htmlspecialchars($vars['step']) ?>">

        <form method="post" action="<?php echo SITE_URL . "/call/edit/" . $call->id ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?php echo $vars['step'] ?>" value="please" />

            <?php echo $steps ?>

            <?php if($vars['step']) echo View::get("call/edit/{$vars['step']}.html.php", array_merge($vars, array('level' => 3, 'next'=>$next_step))) ?>

            <?php echo $steps ?>

        </form>

    </div>

    <?php include __DIR__ . '/../../../../app/view/footer.html.php' ?>

<?php include __DIR__ . '/../../../../app/view/epilogue.html.php' ?>
