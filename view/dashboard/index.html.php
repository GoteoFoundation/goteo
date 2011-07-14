<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'dashboard project-edit';

$user = $_SESSION['user'];

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div class="dashboard-header">
                <a href="/user/<?php echo $user->id; ?>" target="_blank"><img src="/image/<?php echo $user->avatar->id; ?>/56/56" /></a>
                <h2><span>                    <?php if (empty($this['option'])) {
                        echo 'Mi dashboard';
                    } else {
                        echo 'Mi dashboard / ' . $this['menu'][$this['section']]['options'][$this['option']];
                    } ?></span></h2>
            </div>
        </div>

        <?php  echo new View ('view/dashboard/menu.html.php', $this) ?>

        <div id="main" class="<?php echo $this['option'] ?>">
            

            <?php if (!empty($this['message'])) : ?>
                <div class="widget">
                    <?php if (empty($this['section']) && empty($this['option'])) : ?>
                        <h2 class="title">Bienvenid@</h2>
                    <?php endif; ?>
                    <p><?php echo $this['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($this['errors'])) {
                echo implode(',',$this['errors']);
            } ?>

            <?php if (!empty($this['success'])) {
                echo implode(',',$this['success']);
            } ?>

            <?php if (!empty($this['section']) && !empty($this['option'])) {
                echo new View ('view/dashboard/'.$this['section'].'/'.$this['option'].'.html.php', $this);
            } ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';