<?php 

use Goteo\Core\View,
    Goteo\Library\Text;

$currentPost = null;

$bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php echo Text::get('home-banner-header'); ?></h2>
                <strong><?php echo Text::get('home-banner-strong'); ?></strong>
            </div>

        </div>

        <div id="main">
            
            <?php if (!empty($this['posts'])): ?>
            
            <div class="widget learn">
                
            <h2 class="title"><?php echo Text::get('home-posts-header'); ?></h2>
            
                <ul>
                    <?php foreach ($this['posts'] as $post) : ?>
                    <?php if ($this['post'] == $post->id): $currentPost = $post ?>
                    <li><strong><?php echo htmlspecialchars($post->title) ?></strong></li>
                    <?php else: ?>
                    <li><a href="?post=<?php echo $post->id ?>"><?php echo htmlspecialchars($post->title) ?></a></li>
                    <?php endif ?>
                    <?php endforeach ?>
                </ul>
                                                                             
                <div class="post">
                    <h3><?php echo $currentPost->title; ?></h3>
                    <div class="embed"><?php echo $currentPost->media->getEmbedCode(); ?></div>
                    <div class="description">
                        <?php echo $currentPost->text ?>
                    </div>
                </div>                
               
            </div>
            
            <?php endif ?>
            
            <div class="widget projects promos">
                
                <h2 class="title"><?php echo Text::get('home-promotes-header'); ?></h2>
            
                <?php foreach ($this['promotes'] as $promo) : ?>
                
                    <div class="promo">
                                     
                        <!--
                        <div class="balloon">
                            <h4><?php echo htmlspecialchars($promo->title) ?></h4>
                            <blockquote><?php echo $promo->description ?></blockquote>
                        </div>
                            -->                
                        <?php echo new View('view/project/widget/project.html.php', array(
                            'project' => $promo->projectData,
                            'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                                         '<blockquote>' . $promo->description . '</blockquote>'
                        )) ?>
                    
                    </div>
                                        
                <?php endforeach ?>

            </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>