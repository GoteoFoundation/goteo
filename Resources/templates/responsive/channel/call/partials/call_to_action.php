<?php

  $call_to_actions = $this->channel->getCallToActions();

  if ($call_to_actions): 
?>

<div class="section call-to-action">
  <div class="container">
      <div class="row">
        <?php if (count($call_to_actions) == 1): ?>
          <?= $this->insert('channel/call/partials/full_width_call_to_action_widget', ['cta' => $call_to_actions[0]] ) ?>
        <?php else: ?>
          <?php foreach ($call_to_actions as $cta): ?>
            <?= $this->insert('channel/call/partials/call_to_action_widget', ['cta' => $cta] ) ?>
          <?php endforeach; ?>
        <?php endif ?>
      </div>
  </div>
</div>

<?php endif; ?>