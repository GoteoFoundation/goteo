<div class="table-responsive" id="calls-summary" <?= $this->display ? '' : 'style="display: none;"' ?>>
	<table class="table calls-summary">
		<thead>
			<tr>
				<th><?= $this->t('regular-name') ?></th>
				<th><?= $this->t('dashboard-project-pitch-type') ?></th>
				<?php if (!$this->hide_spheres): ?>
					<th><?= $this->text('calls-summary-sphere-header') ?></th>
				<?php endif; ?>
		        <th><?= $this->text('calls-summary-location-header') ?></th>
				<th><?= $this->text('calls-summary-amount-header') ?></th>
				<?php if (!$this->hide_projects): ?>
					<th><?= $this->text('calls-summary-projects-header') ?></th>
				<?php endif; ?>
		        <th><?= $this->pitch ? '' : $this->text('calls-summary-success-header') ?></th>
		    </tr>
		</thead>
		<tbody>
			<?php foreach($this->pitches as $pitch):
                $sphere_name = '';
            ?>
				<?php if($pitch->getTable() === 'matcher'): ?>
					<?= $this->insert('/dashboard/project/partials/pitch/matcher', ['matcher' => $pitch]); ?>
				<?php elseif ($pitch->getTable() === 'node'): ?>
					<?= $this->insert('/dashboard/project/partials/pitch/channel', ['channel' => $pitch]); ?>
				<?php else: ?>
					<?= $this->insert('/dashboard/project/partials/pitch/call', ['call' => $pitch]); ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
