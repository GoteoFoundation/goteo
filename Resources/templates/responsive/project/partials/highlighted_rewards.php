<?php
# Show three rewards in widgets in a responsive way (mobile first)
$project = $this->project;
?>

<div class="row">
    <div class="highlighted-rewards">
        <?= $this->insert('project/partials/reward_widget', ['reward' => $this->individual_rewards[0]]) ?>
        <?= $this->insert('project/partials/reward_widget', ['reward' => $this->individual_rewards[1]]) ?>
        <?= $this->insert('project/partials/reward_widget', ['reward' => $this->individual_rewards[2]]) ?>
    </div>
</div>

