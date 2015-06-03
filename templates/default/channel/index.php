<?php
$channel=$this->channel;
$this->layout("layout", [
    'bodyClass' => 'channel',
    'title' => $channel->name,
    'meta_description' => $channel->description;
    ]);

$this->section('content');

?>

<div id="node-main">

    <!--About section-->

    <div class="patron-profile">
        <div class="avatar">
            <img src="<?php echo $user->avatar->getLink(210, 138, true); ?>" alt="<?php echo $user->name ?>"/><br />
            <!-- enlaces sociales (iconitos como footer) -->
            <ul>
                <?php if (!empty($user->facebook)): ?>
               <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>" target="_blank">F</a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li class="google"><a href="<?php echo htmlspecialchars($user->google) ?>" target="_blank">G</a></li>
                <?php endif ?>
                   <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>" target="_blank">T</a></li>
                <?php endif ?>
                 <?php if (!empty($user->identica)): ?>
                <li class="identica"><a href="<?php echo htmlspecialchars($user->identica) ?>" target="_blank">I</a></li>
                <?php endif ?>
                <?php if (!empty($user->linkedin)): ?>
                <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>" target="_blank">L</a></li>
                <?php endif ?>
            </ul>                   
        </div>              
        <div class="info">
            <!-- Nombre y texto presentación -->
            <p><strong><?php echo $user->name ?></strong> <?php echo $user->about ?></p>
            <!-- 2 webs -->
            <ul>
                <?php $c=0; foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                <?php $c++; if ($c>=2) break; endforeach ?>
            </ul>
        </div>
    </div>

    <div id="side">
    <?php foreach ($vars['side_order'] as $sideitem=>$sideitemName) {
        if (!empty($vars[$sideitem])) 
            echo View::get("node/side/{$sideitem}.html.php", $vars);
            <?=$this->insert("partials/header/banner"); ?>
    } 
    ?>
    </div>

    <div id="content">
    <?php
    
    if (isset($vars['searcher']['promote'])) 
        echo View::get('node/home/promotes.html.php', $vars);

    ?>
    </div>
</div>

<?php $this->section('footer') ?>

<?php if (isset($vars['side_order']['searcher']) || isset($vars['side_order']['categories'])) : ?>
<!-- funcion jquery para mostrar uno y ocultar el resto -->
<script type="text/javascript">
    $(function(){
        $(".show_cat").click(function (event) {
            event.preventDefault();

            if ($("#node-projects-"+$(this).attr('href')).is(":visible")) {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".categories").removeClass('current');
                $(".node-projects").hide();
            } else {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".categories").removeClass('current');
                $(".node-projects").hide();
                $(this).parents('div').addClass('current');
                $("#node-projects-"+$(this).attr('href')).show();
            }

        });
    });
</script>
<?php endif; ?>

<?php $this->append() ?>
