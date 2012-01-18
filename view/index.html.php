<?php 

use Goteo\Core\View,
    Goteo\Library\Text;

$calls     = $this['calls'];
$campaigns = $this['campaigns'];

$bodyClass = 'home';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = Text::widget(Text::get('social-account-facebook'), 'fb');
include 'view/prologue.html.php';
include 'view/header.html.php';
?>
<script type="text/javascript">
    $(function(){
        $('#sub-header').slides();
    });

</script>
<div id="sub-header" class="banners">
    <div class="clearfix">
        <div class="slides_container">
            <!-- Módulo de texto más sign in -->
            <div class="subhead-banner"><?php echo Text::html('main-banner-header'); ?></div>
            <!-- Módulo banner imagen más resumen proyecto -->
            <?php if (!empty($this['banners'])) : foreach ($this['banners'] as $id=>$banner) : ?>
            <div class="subhead-banner"><?php echo new View('view/header/banner.html.php', array('banner'=>$banner)); ?></div>
            <?php endforeach;
            else : ?>
            <div class="subhead-banner"><?php echo Text::html('main-banner-header'); ?></div>
            <?php endif; ?>
        </div>
        <div class="mod-pojctopen"><?php echo Text::html('open-banner-header', $fbCode); ?></div>
    </div>
    <div class="sliderbanners-ctrl">
        <a class="prev">prev</a>
        <ul class="paginacion"></ul>
        <a class="next">next</a>
    </div>
</div>
<div id="main">

    <?php if (!empty($this['posts'])): ?>
    <script type="text/javascript">
        $(function(){
            $('#learn').slides({
                container: 'slder_container',
                paginationClass: 'slderpag',
                generatePagination: false,
                play: 0
            });
        });
    </script>
    <div id="learn" class="widget learn">
        <h2 class="title"><?php echo Text::get('home-posts-header'); ?></h2>
        <div class="slder_container"<?php if (count($this['posts'])==1) echo ' style="display:block;"'; ?>>

            <?php $i = 1; foreach ($this['posts'] as $post) : ?>
            <div class="slder_slide">
                <div class="post" id="home-post-<?php echo $i; ?>" style="display:block;">
                    <?php  if (!empty($post->media->url)) : ?>
                        <div class="embed">
                            <?php echo $post->media->getEmbedCode(); ?>
                        </div>
                    <?php elseif (!empty($post->image)) : ?>
                        <div class="image">
                            <img src="<?php echo $post->image->getLink(500, 285); ?>" alt="Imagen"/>
                        </div>
                    <?php endif; ?>
                    <h3><?php echo $post->title; ?></h3>
                    <div class="description">
                <?php echo Text::recorta($post->text, 600) ?>
                    </div>

                    <div class="read_more"><a href="/blog/<?php echo $post->id; ?>"><?php echo Text::get('regular-read_more') ?></a></div>
                </div>
            </div>
            <?php $i++; endforeach; ?>
        </div>
        <a class="prev">prev</a>
        <ul class="slderpag">
            <?php $i = 1; foreach ($this['posts'] as $post) : ?>
            <li><a href="#" id="navi-home-post-<?php echo $i ?>" rel="home-post-<?php echo $i ?>" class="tipsy navi-home-post" title="<?php echo htmlspecialchars($post->title) ?>">
                <?php echo htmlspecialchars($post->title) ?></a>
            </li>
            <?php $i++; endforeach ?>
        </ul>
        <a class="next">next</a>

    </div>

    <?php endif; ?>

    <?php if (!empty($this['promotes'])): ?>
    <div class="widget projects">

        <h2 class="title"><?php echo Text::get('home-promotes-header'); ?></h2>

        <?php foreach ($this['promotes'] as $promo) : ?>

                <?php echo new View('view/project/widget/project.html.php', array(
                    'project' => $promo->projectData,
                    'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                                 '<blockquote>' . $promo->description . '</blockquote>'
                )) ?>

        <?php endforeach ?>

    </div>
    <?php endif; ?>

    <?php if (!empty($calls) || !empty($campaigns)): ?>
    <div class="widget calls">

        <div class="title">
            <div class="logo"><?php echo Text::get('home-calls-header'); ?></div>
            <?php if (!empty($calls)) : ?>
            <div class="call-count mod1">
                <strong><?php echo count($calls) ?></strong>
                <span>Convocatorias<br />abiertas</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($campaigns)) : ?>
            <div class="call-count mod2">
                <strong><?php echo count($campaigns) ?></strong>
                <span>Campañas<br />activas</span>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($calls)) : ?>
        <script type="text/javascript">
            $(function(){
                $('#calls').slides({
                    container: 'slder_calls',
                    generatePagination: false,
                    play: 0
                });
            });
        </script>
        <div id="calls" class="callrow">
            <?php if (count($calls) > 1) : ?><a class="prev">prev</a><?php endif ?>
            <div class="slder_calls">
            <?php foreach ($calls as $call) : ?>
                <div class="slder_slide">
                <?php echo new View('view/call/widget/call.html.php', array('call' => $call)); ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php if (count($calls) > 1) : ?><a class="next">next</a><?php endif ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($campaigns)) : ?>
        <script type="text/javascript">
            $(function(){
                $('#campaigns').slides({
                    container: 'slder_campaigns',
                    generatePagination: false,
                    play: 0
                });
            });
        </script>
        <div id="campaigns" class="callrow">
            <?php if (count($campaigns) > 1) : ?><a class="prev">prev</a><?php endif ?>
            <div class="slder_campaigns">
            <?php foreach ($campaigns as $call)  : ?>
                <div class="slder_slide">
                <?php echo new View('view/call/widget/call.html.php', array('call' => $call)); ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php if (count($campaigns) > 1) : ?><a class="next">next</a><?php endif ?>
        </div>
        <?php endif; ?>

        <a class="all" href="/discover/calls"><?php echo Text::get('regular-see_all'); ?></a>
    </div>
    <?php endif; ?>

</div>
<?php include 'view/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>