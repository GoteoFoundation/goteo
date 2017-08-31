<?php
$reward = $this->reward_item;
$selected = $selected;
$available = $reward->available();
?>
    <form class="form-horizontal" role="form" method="GET" action="/invest/<?= $this->project->id ?>/payment">

        <div class="row padding-sm no-padding col-sm-10 col-sm-offset-1">
            <label class="label-reward <?= $selected ? 'reward-choosen' : '' ?><?= $available ? '' : ' label-disabled' ?>" for="reward-<?= $reward->id?>">
            <div class="col-sm-2 no-padding">
                <input name="reward" class="reward" id="reward-<?= $reward->id?>" <?= $selected ? ' checked="checked"' : '' ?><?= $available ? '' : ' disabled' ?> value="<?= $reward->id ?>" type="radio">
                <strong class="reward-amount"><?= amount_format($reward->amount) ?></strong>
            </div>
            <div class="col-sm-9">
                <strong><?= $reward->reward ?></strong>
                <div class="reward-description">
                <?= $reward->description ?>
                </div>
                <div style="margin-top:10px">
                    <?php if (!$available) : // no quedan ?>
                    <span class="limit-reward"><?= $this->text('invest-reward-none') ?></span>
                        <?php elseif (!empty($reward->units)) : // unidades limitadas ?>
                        <strong class="limit-reward"><?= $this->text('project-rewards-individual_reward-limited');?></strong><br>
                        <?php $units = ($reward->units - $reward->taken); // resto?>
                        <span class="limit-reward-number">
                        <?= $this->text('project-rewards-individual_reward-units_left', $units) ?><br>
                        </span>
                    <?php endif ?>
                </div>
            </div>

            <div class="amount-box">
                <div class="col-sm-1 no-padding sm-display-none">
                    <img class="img-responsive reward-icon" src="<?= SRC_URL ?>/assets/img/rewards/<?= $reward->icon ?>.svg">
                </div>

                <div class="row col-sm-10 col-sm-offset-2 margin-2" id="amount-container">
                    <div class="col-sm-1 col-sm-offset-1 no-padding col-xs-1">
                        <strong class="reward-amount"><?= $this->get_currency('html') ?></strong>
                    </div>
                    <div class="no-padding container-input-amount col-sm-4 col-xs-10">
                        <input type="number" class="form-control input-amount" name="amount" value="<?= $this->amount ? $this->amount : amount_format($reward->amount, 0, true) ?>" id="amount" min="<?= amount_format($reward->amount, 0, true) ?>" required>
                    </div>
                    <div class="col-sm-5 col-sm-offset-1 reward-button">
                        <button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('invest-button') ?></button>
                    </div>
                </div>
            </div>

            </label>

        </div>

    </form>
