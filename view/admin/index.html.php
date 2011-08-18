<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
            </div>
        </div>

        <div id="main">

            <div class="center">

                <?php foreach ($this['menu'] as $sCode=>$section) : ?>
                <div class="widget board collapse">
                    <h3 class="title"><?php echo $section['label'] ?></h3>
                    <ul>
                        <?php foreach ($section['options'] as $oCode=>$option) :
                            echo '<li><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>
                                ';
                        endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
