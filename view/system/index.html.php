<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\Model;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header" style="margin-bottom: 10px;">
            <div class="breadcrumbs">System</div>
        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <div id="main">

            <div class="admin-center">

            <div class="admin-menu">
                <fieldset>
                    <legend>Tools</legend>
                    <ul>
                        <li><a href="/system/lasts">Last 10 users</a></li>
                    </ul>
                </fieldset>
            </div>

            <div class="widget">
                <?php foreach ($this['data'] as $item) {
                    echo \trace($item);
                } ?>
            </div>


            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
