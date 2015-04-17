<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Application\Lang;

// TODO: mejorar esto
// hay que elegir un idioma al que traducir, no se puede traducir a español, español es el idioma original
if ($_SESSION['translate_lang'] == 'es') {
    unset($_SESSION['translate_lang']);
    unset($vars['section']);
    unset($vars['action']);
}

$bodyClass = 'admin';

include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php'; ?>

        <div id="sub-header" style="margin-bottom: 10px;">
            <div class="breadcrumbs"><?php echo defined('ADMIN_BCPATH') ? ADMIN_BCPATH : "<strong>Traductor</strong>"; ?></div>
        </div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main">

            <div class="widget">
                <?php echo View::get('translate/langs/selector.html.php', $vars); ?>
            </div>

            <?php if (!empty($vars['errors'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode('<br />', $vars['errors']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($vars['section']) && !empty($vars['action'])) :
                echo View::get('translate/'.$vars['section'].'/'.$vars['action'].'.html.php', $vars);
            else :
                foreach ($vars['menu'] as $sCode=>$section) :
                    if ($sCode == 'node') continue;
                    ?>
                    <a name="<?php echo $sCode ?>"></a>
                    <div class="widget board collapse">
                        <h3 class="title"><?php echo $section['label'] ?></h3>
                        <ul>
                            <?php foreach ($section['options'] as $oCode=>$option) :
                                echo '<li><a href="/translate/'.$oCode.'">'.$option['label'].'</a></li>
                                    ';
                            endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach;

            endif; ?>

        </div>
<?php
include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
