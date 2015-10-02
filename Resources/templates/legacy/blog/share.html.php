<?php
use Goteo\Library\Text;

$urls = $vars['urls'];
?>
<ul class="share-goteo">
    <li class="sharetext"><?php echo Text::get('regular-share_this'); ?></li>
<?php if (!empty($urls['twitter'])) : ?><li class="twitter"><a href="<?php echo htmlspecialchars($urls['twitter']) ?>" target="_blank"><?php echo Text::get('regular-twitter'); ?></a></li><?php endif; ?>
<?php if (!empty($urls['facebook'])) : ?><li class="facebook"><a href="<?php echo htmlspecialchars($urls['facebook']) ?>" target="_blank"><?php echo Text::get('regular-facebook'); ?></a></li><?php endif; ?>
</ul>
