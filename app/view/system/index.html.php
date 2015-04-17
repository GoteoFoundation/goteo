<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\Model;

$bodyClass = 'admin';

include __DIR__ . '/../prologue.html.php';

    include __DIR__ . '/../header.html.php'; ?>

        <div id="sub-header" style="margin-bottom: 10px;">
            <div class="breadcrumbs">System</div>
        </div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

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
                <?php foreach ($vars['data'] as $item) {
                    echo \trace($item);
                } ?>
            </div>


            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php
    include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
