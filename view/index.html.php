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

    <?php if (!empty($this['posts'])) echo new View('view/home/posts.html.php', $this) ?>

    <?php if (!empty($this['promotes'])) echo new View('view/home/promotes.html.php', $this) ?>

    <?php if (!empty($this['calls']) || !empty($this['campaigns'])) echo new View('view/home/calls.html.php', $this) ?>

    <?php if (!empty($this['feed'])) echo new View('view/home/feed.html.php', $this) ?>

</div>
<?php include 'view/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>