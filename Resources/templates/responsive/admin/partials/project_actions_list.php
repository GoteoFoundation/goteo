<li><form method="post" action="/admin/users/impersonate/<?= $this->project->owner ?>">
<input type="hidden" name="id" value="<?= $this->project->owner ?>">
<button type="submit"><i class="fa fa-user-md"></i> <?= $this->text('admin-impersonate-user', $this->project->getOwner()->name) ?></button>
</form></li>

<?php if (!$this->project->isApproved()) : ?>
    <li><a href="<?php echo "/admin/projects/reject/{$this->project->id}" ?>" title="<?= $this->text('admin-project-express-discard-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-express-discard-sure'),'js') ?>');"><i class="fa fa-ban"></i> <?= $this->text('admin-project-express-discard') ?></a></li>
   	<li>
   		<a href="<?php echo "/admin/projects/derivation/{$this->project->id}" ?>" title="<?= $this->text('admin-project-derivation-discard-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-derivation-discard-sure'),'js') ?>');"><i class="fa fa-sign-out"></i> <?= $this->text('admin-project-derivation-discard') ?></a>
   	</li>

    <li><a href="<?php echo "/admin/projects/publish/{$this->project->id}" ?>" title="<?= $this->text('admin-project-to-review-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-to-publish-sure'), 'js') ?>');"><i class="fa fa-play"></i> <?= $this->text('admin-project-to-publish') ?></a></li>


<?php endif ?>

<?php if ($this->project->inEdition()): ?>
    <li><a href="<?php echo "/admin/projects/review/{$this->project->id}" ?>" title="<?= $this->text('admin-project-to-review-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-to-review-sure'), 'js') ?>');"><i class="fa fa-paper-plane-o"></i> <?= $this->text('admin-project-to-review') ?></a></li>
    <?php endif ?>

<?php if ($this->project->inReview() || $this->project->inCampaign()) : ?>
    <li><a href="<?php echo "/admin/projects/enable/{$this->project->id}" ?>" title="<?= $this->text('admin-project-to-negotiation-desc') ?>"<?php if ($this->project->inEdition()) : ?> onclick="return confirm('<?= $this->ee($this->text('admin-project-to-negotiation-sure'), 'js') ?>'');"<?php endif ?>><i class="fa fa-briefcase"></i> <?= $this->text('admin-project-to-negotiation') ?></a></li>
<?php endif ?>
