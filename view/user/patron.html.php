<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'home patron';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$recos = $this['recos'];
?>

<div id="sub-header">
    <div>
        <h2><span class="greenblue"><?php echo $user->name ?></span><br /><?php echo Text::html('profile-patron-header', count($recos)) ?></h2>
    </div>
    
    <?php if (!empty($user->node) && $user->node != \GOTEO_NODE) : ?>
    <div class="nodemark"><a class="node-jump" href="<?php echo $user->nodeData->url ?>" >NODO: <?php echo $user->nodeData->name ?></a></div>
    <?php endif; ?>
</div>

<div id="main">

    <div class="patron-profile">
        <div class="avatar">
            <img src="<?php echo $user->avatar->getLink(210, 138, true); ?>" alt="<?php echo $user->name ?>"/><br />
            <!-- enlaces sociales (iconitos como footer) -->
            <ul>
                <?php if (!empty($user->facebook)): ?>
                <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>" target="_blank">F</a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li class="google"><a href="<?php echo htmlspecialchars($user->google) ?>" target="_blank">G</a></li>
                <?php endif ?>
                <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>" target="_blank">T</a></li>
                <?php endif ?>
                <?php if (!empty($user->identica)): ?>
                <li class="identica"><a href="<?php echo htmlspecialchars($user->identica) ?>" target="_blank">I</a></li>
                <?php endif ?>
                <?php if (!empty($user->linkedin)): ?>
                <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>" target="_blank">L</a></li>
                <?php endif ?>
            </ul>
        </div>
        <div class="info">
            <!-- Nombre y texto presentación -->
            <p><strong><?php echo $user->name ?></strong> <?php echo $user->about ?></p>
            <!-- 2 webs -->
            <ul>
                <?php $c=0; foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                <?php $c++; if ($c>=2) break; endforeach ?>
            </ul>
        </div>
    </div>
    <br clear="all" />

    <div class="widget projects">

        <?php foreach ($recos as $reco) : ?>

                <?php echo new View('view/project/widget/project.html.php', array(
                    'project' => $reco->projectData,
                    'balloon' => '<h4>' . htmlspecialchars($reco->title) . '</h4>' .
                                 '<blockquote>' . $reco->description . '</blockquote>'
                )) ?>

        <?php endforeach ?>

    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
