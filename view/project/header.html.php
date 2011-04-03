<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">

<?php use Goteo\Library\Text;
$status = Goteo\Model\Project::status();
?>
<p><a href="/dashboard">[DASHBOARD]</a></p>
<hr />
<p>ESTADO DEL PROYECTO <?php echo $status[$project->status]; ?> | ESTADO GLOBAL DE LA INFORMACIÓN: <?php echo $project->progress . '%'; ?></p>
<span><?php echo Text::get('explain project progress'); ?></span><br />
<hr />
<a href="/project/user"><?php echo Text::get('step 1'); ?></a>&nbsp;
<a href="/project/register"><?php echo Text::get('step 2'); ?></a>&nbsp;
<a href="/project/edit"><?php echo Text::get('step 3'); ?></a>&nbsp;
<a href="/project/costs"><?php echo Text::get('step 4'); ?></a>&nbsp;
<a href="/project/rewards"><?php echo Text::get('step 5'); ?></a>&nbsp;
<a href="/project/supports"><?php echo Text::get('step 6'); ?></a>&nbsp;
<a href="/project/overview"><?php echo Text::get('step 7'); ?></a>&nbsp;
<hr />