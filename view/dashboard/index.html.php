<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'dashboard project-edit';

$user = $_SESSION['user'];

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div class="dashboard-header">
                <a href="/user/<?php echo $user->id; ?>" target="_blank"><img src="<?php echo $user->avatar->getLink(56, 56, true); ?>" /></a>
                <h2><span>                    <?php if (empty($this['option'])) {
                        echo Text::get('dashboard-header-main');
                    } else {
                        echo Text::get('dashboard-header-main') . ' / ' . $this['menu'][$this['section']]['options'][$this['option']];
                    } ?></span></h2>
            </div>
        </div>

        <?php  echo new View ('view/dashboard/menu.html.php', $this) ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <div id="main" class="<?php echo $this['option'] ?>">

<?php if ($this['section'] == 'projects') echo new View ('view/dashboard/projects/selector.html.php', $this); ?>
<?php if ($this['section'] == 'translates') echo new View ('view/dashboard/translates/selector.html.php', $this); ?>

            <?php if (!empty($this['message'])) : ?>
                <div class="widget">
                    <p><?php echo $this['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($this['errors'])) : ?>
                <div class="widget" style="color:red;">
                    <p><?php echo implode('<br />',$this['errors']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($this['success'])) : ?>
                <div class="widget">
                    <p><?php echo implode('<br />',$this['success']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($this['section']) && !empty($this['option'])) {
                echo new View ('view/dashboard/'.$this['section'].'/'.$this['option'].'.html.php', $this);
            } ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';
