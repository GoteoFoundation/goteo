<?php
use Goteo\Library\Text;

$social = $vars['social'];
?>

<div id="side" class="twitter">
    <h2><?php echo Text::get('call-header-buzz'); ?></h2>
	<div class="tweets-container">
	<div class="tweets">
<?php
// Petición a twitter se desconecta en la Linea 448 en controller/call.php
if ($_SESSION['user']->id == 'root') echo '<!-- BUZZ_DEBUG:: '. $social->buzz_debug . ' -->';

foreach ($social->buzz as $item) : ?>
    <div class="tweet">
        <div class="avatar">
            <a href="<?php echo $item->profile ?>" target="_blank">
                <img src="<?php echo $item->avatar ?>" alt="<?php echo $item->author ?>" title="<?php echo $item->user ?>"/>
            </a>
        </div>
        <div class="text">
            <strong><a href="<?php echo $item->profile ?>" target="_blank"><?php echo $item->user ?></a></strong>
            <br />
            <a href="<?php echo 'https://twitter.com/'.$item->twitter_user ?>" target="_blank"><?php echo '@'.$item->twitter_user ?></a>
                <blockquote><?php echo $item->text ?></blockquote>
        </div>
    </div>
<?php endforeach;
?>
	</div>
	</div>
</div>

