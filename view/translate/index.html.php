<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Library\Lang;

$langs = Lang::getAll();

/*
 * Ya no hacemos esto, hay que elegir un idioma al que traducir, nos e puede traducir a español, español es el idioma original
if (!isset ($_SESSION['translator_lang'])) {
    $_SESSION['translator_lang'] = GOTEO_DEFAULT_LANG;
}
 *
 */
if ($_SESSION['translator_lang'] == 'es') {
    unset($_SESSION['translator_lang']);
    unset($this['section']);
    unset($this['option']);
}


$bodyClass = 'admin';

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de traducción</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/translate">Portada</a></li>
                        <li class="checking"><a href="/translate/texts">Textos</a></li>
                        <li class="accounting"><a href="/translate/pages">Páginas</a></li>
                        <li class="messages"><a href="/translate/contents">Contenidos</a></li>
                    </ul>
                </div>
            </div>

        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <div id="main">
            
            <div class="widget">
                <?php echo new View ('view/translate/langs/selector.html.php', $this); ?>
            </div>

            <?php if (!empty($this['errors'])) : ?>
                <div class="widget">
                    <?php echo implode(',',$this['errors']); ?>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($_SESSION['translator_lang']) && !empty($this['section']) && !empty($this['option'])) {
                echo new View ('view/translate/'.$this['section'].'/'.$this['option'].'.html.php', $this);
            } else {
                echo '<div class="widget">' . Text::get('translate-home-guide') . '</div>';
            } ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';