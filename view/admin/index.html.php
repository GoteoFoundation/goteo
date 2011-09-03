<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL;

$bodyClass = 'admin';

$message = '';
if (!empty($this['errors']) || !empty($this['success'])) {
    $message = '<div class="widget"><p>'.implode('<br />', $this['errors']).implode('<br />', $this['success']).'</p></div>';
}

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
                <?php if (defined('ADMIN_BCPATH')) : ?>
                <blockquote><?php echo ADMIN_BCPATH; ?></blockquote>
                <?php endif; ?>
            </div>
        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <?php if (!empty($this['folder']) && !empty($this['file'])) : ?>

        <div id="main">

            <?php echo $message; ?>
            
<?php
                if ($this['folder'] == 'base') {
                    $path = 'view/admin/'.$this['file'].'.html.php';
                } else {
                    $path = 'view/admin/'.$this['folder'].'/'.$this['file'].'.html.php';
                }

                echo new View ($path, $this);
?>

<?php   else : ?>

        <div id="main">

            <div class="center">

                <?php echo $message; ?>

                <?php if (ACL::check('/translate')) : ?>
                <div class="widget">
                    <?php echo new View ('view/admin/selector.html.php', $this); ?>
                </div>
                <?php endif; ?>

                <?php foreach ($this['menu'] as $sCode=>$section) : ?>
                <a name="<?php echo $sCode ?>"></a>
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


            <div class="side">
                <a name="feed"></a>
                <div class="widget feed">
                    FEED lateral.  Arreglarlo para que salga en la columan lateral como en la pagina del usuario<br /><br />
                    <a href="/admin/feed/<?php echo isset($_GET['feed']) ? '?feed='.$_GET['feed'] : ''; ?>">Ver más</a>

                    <p>
                        <a href="/admin/?feed=all#feed">TODO</a> <a href="/admin/?feed=admin#feed">ADMINISTRADOR</a> <a href="/admin/?feed=user#feed">USUARIO</a>
                        <br /><br />
                        Ver Feeds <?php echo $_GET['feed']; ?>
                    </p>
                </div>
            </div>


            <?php endif; ?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
