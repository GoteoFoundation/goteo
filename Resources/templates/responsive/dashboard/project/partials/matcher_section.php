<?php
if($this->project && $matchers = $this->project->getMatchers()):
    foreach($matchers as $matcher):
        if(!$matcher->active) continue;
        $status = $matcher->getProjectStatus($this->project);
        if(!in_array($status, ['pending', 'accepted', 'active'])) continue;

?>
    <blockquote>
        <p><i class="fa fa-hand-o-right"></i> <?= $this->text('matcher-apply-' . $status, '<a href="/matcher/' . $matcher->id . '"><strong>' . $matcher->name . '</strong></a>') ?></p>
        <?php if($status === 'pending'): ?>
            <p><?= $this->text('matcher-apply-pending-desc', '<a data-toggle="modal" data-target="#termsModal-' . $matcher->id . '" href="#"><strong>' . $this->text('matcher-terms') . '</strong></a>') ?>:</p>
            <p>
                <a href="/dashboard/ajax/matchers/<?= $matcher->id ?>/accept/<?= $this->project->id ?>" class="btn btn-orange btn-lg"><i class="fa fa-thumbs-o-up"></i> <?= $this->text('matcher-apply-accept') ?></a>
                <a href="/dashboard/ajax/matchers/<?= $matcher->id ?>/reject/<?= $this->project->id ?>" class="btn btn-default btn-lg" onclick="return confirm('<?= $this->ee($this->text('matcher-apply-reject-sure'))?>')"><i class="fa fa-thumbs-o-down"></i> <?= $this->text('matcher-apply-reject') ?></a>
            </p>
        <?php endif ?>
    </blockquote>

    <!-- Modal -->
    <div class="modal fade" id="termsModal-<?= $matcher->id ?>" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title"><?= $this->text('matcher-terms-desc') ?></h3>
          </div>
          <div class="modal-body"><?= $this->markdown($matcher->terms) ?></div>
        </div>
      </div>
    </div>

<?php endforeach ?>
<?php endif ?>
