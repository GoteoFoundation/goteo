<?php
$channel=$this->channel;
$section = $channel->getSections('footer');
if ($section):
?>
    <?= $this->insert('channel/call/partials/minimal_footer'); ?>
<?php else: ?>
    <?= $this->insert('channel/call/partials/full_footer'); ?>
<?php endif; ?>
