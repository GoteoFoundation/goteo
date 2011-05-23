<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">

            <?php foreach ($this['posts'] as $post) : ?>
                <div>
                    <?php if ($this['post'] == $post->id) : ?>
                        <h3><?php echo $post->title; ?></h3>
                        <div><?php echo $post->media->getEmbedCode(); ?></div>
                        <p><?php echo $post->description; ?></p>
                    <?php else : ?>
                        <a href="?post=<?php echo $post->id; ?>">Ver el <?php echo $post->order; ?></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <hr />

            <?php foreach ($this['promotes'] as $promo) : ?>
                <div>
                    <div>
                        <h3><?php echo $promo->title; ?></h3>
                        <p><?php echo $promo->description; ?></p>
                    </div>
                    <div>
                        <!-- la instancia del proyecto está en $promo->projectData y se pintan con el mismo widget de discover -->
                        <span><?php echo $promo->name; ?></span><br />
                        <a href="/invest/<?php echo $promo->project; ?>">Apóyalo</a>
                        <a href="/project/<?php echo $promo->project; ?>">Ver proyecto</a>
                    </div>
                </div>
            <?php endforeach; ?>


        </div>        


        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>