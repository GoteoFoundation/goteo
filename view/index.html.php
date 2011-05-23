<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">

            <?php foreach ($this['posts'] as $post) : ?>
                <div>
                    <h3><?php echo $post->title; ?></h3>
                    <div><?php echo $post->media->getEmbedCode(); ?></div>
                    <p><?php echo $post->description; ?></p>
                </div>
            <?php endforeach; ?>
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>