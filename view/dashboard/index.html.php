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
                    <?php echo $this['menu'][$this['section']]['label'] . ' / ' . $this['menu'][$this['section']]['options'][$this['option']]; ?><br />
                    <em><?php echo $user->name; ?></em></h2>
            </div>
        </div>

        <div id="main">

            <?php 
            echo new View ('view/dashboard/menu.html.php', $this);
            ?>
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <?php
            echo new View ('view/dashboard/'.$this['section'].'/'.$this['option'].'.html.php', $this);
            ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';