<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'dashboard';

$user = $_SESSION['user']->id;

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" />
                    <?php if (empty($this['option'])) {
                        echo 'Mi dashboard / Bienvenid@';
                    } else {
                        echo 'Mi dashboard / ' . $this['menu'][$this['section']]['options'][$this['option']];
                    } ?><br />
                    <em><?php echo $user->name; ?></em></h2>
            </div>
        </div>

        <div id="main">

            <?php 
            echo new View ('view/dashboard/menu.html.php', $this);
            ?>

            <?php if (!empty($this['message'])) : ?>
                <div class="widget">
                    <h2 class="title">Bienvenid@</h2>
                    <p><?php echo $this['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['section']) && !empty($this['option'])) {
                echo new View ('view/dashboard/'.$this['section'].'/'.$this['option'].'.html.php', $this);
            } ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';