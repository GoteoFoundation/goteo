<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'review';

$review = $this['review'];

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Informe de revisión del proyecto 'tal'</h2>
            </div>
        </div>

        <div id="main">
            <div class="widget">
                <pre><?php echo print_r($review, 1); ?></pre>
            </div>
        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';