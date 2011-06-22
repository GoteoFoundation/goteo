<?php

use Goteo\Library\Text;

$bodyClass = 'community';

include 'view/prologue.html.php';

include 'view/header.html.php';

$read_more = Text::get('regular-read_more');

?>

        <div id="main">

            <h2>Comunidad Goteo <?php echo $this['name']; ?></h2>
            <p><?php echo $this['title']; ?></p>

            <div id="content"><?php echo $this['content']; ?></div>

            <?php foreach ($this['news'] as $id=>$content) : ?>
                <div>
                    <h3><?php echo $content->title; ?></h3>
                    <blockquote><?php echo $content->description; ?></blockquote>
                    <a href="<?php echo $content->url; ?>"><?php echo $read_more; ?></a>
                </div>
            <?php endforeach; ?>
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>