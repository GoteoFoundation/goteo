<?php
$reward = $this->reward_item;
$selected = $this->selected;
$available = $reward->available();
?>
    <form class="form-horizontal" role="form" method="GET" action="/invest/<?= $this->project->id ?>/payment">
        <div class="row padding-sm no-padding col-sm-10 col-sm-offset-1 reward-item">
            <?php
                $matchedReward = $reward->isMatched();
            ?>
            <label class="label-reward <?= $selected ? 'reward-choosen' : '' ?><?= $available ? '' : ' label-disabled' ?> <?= $matchedReward ? 'matched-reward' : ''?>" for="reward-<?= $reward->id ?>">

                <?php if ($matchedReward): ?>
                    <div class="btn-lilac">
                        <i class="icon icon-call"></i> <?= $this->t('regular-call') ?>
                        <i class="fa fa-info-circle"
                           data-html="true"
                           data-container="body"
                           data-toggle="tooltip"
                           title=""
                           data-original-title="<span style='font-size: 16px;'><?= $this->text('matcher-matches-this-reward') ?></span>">
                        </i>
                    </div>
                <?php endif; ?>
                <div class="col-sm-2 no-padding">
                    <input name="reward" class="reward" id="reward-<?= $reward->id?>" <?= $selected ? ' checked="checked"' : '' ?><?= $available ? '' : ' disabled' ?> value="<?= $reward->id ?>" type="radio">
                    <strong class="reward-amount"><?= amount_format($reward->amount) ?></strong>
                </div>
                <div class="col-sm-8">
                    <strong><?= $reward->reward ?></strong>
                    <div class="reward-description">
                        <?= $this->markdown($reward->description) ?>
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
                    <div class="row col-sm-10 col-sm-offset-2 margin-2" id="amount-container">
                        <div class="col-sm-1 col-sm-offset-1 no-padding col-xs-1">
                            <label id="amount-<?= $reward->id ?>"class="reward-amount" for="amount-<?= $reward->id?>"><?= $this->get_currency('html') ?></label>
                        </div>
                        <div class="no-padding container-input-amount col-sm-4 col-xs-10">
                            <input type="number"
                                   class="form-control input-amount"
                                   name="amount"
                                   value="<?= $this->amount ? $this->amount : amount_format($reward->amount, 0, true) ?>"
                                   id="amount-<?= $reward->id ?>"
                                   min="<?= amount_format($reward->amount, 0, true) ?>"
                                   title="<?= $this->t('regular-amount') ?>"
                                   aria-labelledby="amount-<?= $reward->id ?>"
                                   required>
                        </div>
                        <div class="col-sm-5 col-sm-offset-1 reward-button">
                            <button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('invest-button') ?></button>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </form>
