<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'review';

$user = $_SESSION['user'];

include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

/*
 *
 <img src="<?php echo $user->avatar->getLink(75, 75, true); ?>" /><br />
                    <em><?php echo $user->name; ?></em>
 *
 */

?>

        <div id="sub-header">
            <div>
                <h2>
                    <?php echo 'Mi panel de revisor / ' . $vars['menu'][$vars['section']]['label']; ?></h2>
            </div>
        </div>

        <?php  echo View::get('review/menu.html.php', $vars) ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main">


            <?php if (!empty($vars['message'])) : ?>
                <div class="widget">
                    <?php if (empty($vars['section']) && empty($vars['option'])) : ?>
                        <h2 class="title">Bienvenid@</h2>
                    <?php endif; ?>
                    <p><?php echo $vars['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($vars['errors'])) {
                echo implode(',',$vars['errors']);
            } ?>

            <?php if (!empty($vars['success'])) {
                echo implode(',',$vars['success']);
            } ?>

            <?php if (!empty($vars['section']) && !empty($vars['option'])) {
                echo View::get('review/'.$vars['section'].'/'.$vars['option'].'.html.php', $vars);
            } ?>

        </div>
<?php
include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
