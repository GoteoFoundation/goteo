<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'dashboard project-edit';

$user = $_SESSION['user'];

$option = str_replace(array('call_overview', 'node_overview'), array('overview', 'overview'), $vars['option']);

if ($option == 'location') $jsreq_autocomplete = true;

// funcionalidades con ckeditor
$jsreq_ckeditor = $vars['ckeditor'];
$superform = true;
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php'; ?>

        <div id="sub-header">
            <div class="dashboard-header">
                <a href="/user/<?php echo $user->id; ?>" target="_blank"><img src="<?php echo $user->avatar->getLink(56, 56, true); ?>" /></a>
                <h2><span><?php if (empty($option)) {
                        echo Text::get('dashboard-header-main');
                    } else {
                        echo Text::get('dashboard-header-main') . ' / ' . $vars['menu'][$vars['section']]['label'] . ' / ' . $vars['menu'][$vars['section']]['options'][$option];
                    } ?></span></h2>
            </div>
        </div>

        <?php  echo View::get('dashboard/menu.html.php', $vars) ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main" class="<?php echo $vars['option'] ?>">

            <?php if ($vars['section'] == 'projects') echo View::get('dashboard/projects/selector.html.php', $vars); ?>
            <?php if ($vars['section'] == 'calls') echo View::get('dashboard/calls/selector.html.php', $vars); ?>
            <?php if ($vars['section'] == 'translates') echo View::get('dashboard/translates/selector.html.php', $vars); ?>

            <?php if (!empty($vars['message'])) : ?>
                <div class="widget">
                    <p><?php echo $vars['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($vars['errors'])) : ?>
                <div class="widget" style="color:red;">
                    <p><?php echo implode('<br />',$vars['errors']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($vars['success'])) : ?>
                <div class="widget">
                    <p><?php echo implode('<br />',$vars['success']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($vars['section']) && !empty($vars['option'])) {
                echo View::get('dashboard/'.$vars['section'].'/'.$vars['option'].'.html.php', $vars);
            } ?>

        </div>
<?php
include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
