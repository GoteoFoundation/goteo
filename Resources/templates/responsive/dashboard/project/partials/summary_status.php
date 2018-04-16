<?php

$status_text = '';
$status_class = 'cyan';
$desc = '';
$project = $this->project;
if (!$project->isApproved()){
    // Project will be published automatically if date is present
    if(!empty($project->published)) {
        if ($project->published > date('Y-m-d')) {
            // si la fecha es en el futuro, es que se publicará
            $status_text = $this->text('project-willpublish', date('d/m/Y', strtotime($project->published)));
            $status_class = 'orange';
        } else {
            // si la fecha es en el pasado, es que la campaña ha sido cancelada
            $status_text = $this->text('project-unpublished');
            $status_class = 'danger';
        }
    } else {
        // Not published yet
        if($project->inReview()) {
            $status_class = 'lilac';
            $desc = $this->text('form-project_waitfor-review');
            $status_text = $this->text('project-reviewing');
        }
        else {
            $status_text = $this->text('project-not_published');
            $status_class = 'danger';
        }
    }
}

?>

<?php if ($status_text): ?>
    <div class="spacer alert alert-<?= $status_class ?>"><?= $status_text ?></div>
<?php endif ?>
<?php if ($desc): ?>
    <blockquote><?= $desc ?></blockquote>
    <?php if($project->inReview()): ?>
        <blockquote>
            <p><?= $this->text('dashboard-project-add-translations') ?></p>

            <p><a href="/dashboard/project/<?= $project->id ?>/translate"><i class="fa fa-hand-o-right"></i> <?= $this->text('form-navigation_bar-header') ?> <?= $this->text('regular-translations') ?></a></p>
        </blockquote>
    <?php endif ?>

<?php endif ?>

