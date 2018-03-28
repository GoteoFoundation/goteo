<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>
<div class="dashboard-content">

  <div class="inner-container">
<?php if($this->mails): ?>
    <h2><?= $this->text('dashboard-mail-sent-mails', $this->mails[0]->email) ?></h2>
    <p><?= $this->text('dashboard-mail-sent-mails-desc') ?></p>
    <table class="table">
    <?php
    foreach($this->mails as $mail):

        // Track only if the users is the owner
        $token = $mail->getToken($this->get_user()->email === $mail->email);
        $stats = $mail->getStats();
        $opened = (bool) $stats && $stats->getEmailOpenedCollector()->getPercent();
    ?>

    <tr>
        <td><?= $mail->id ?></td>
        <td><?= \date_formater($mail->date, true) ?></td>
        <td><?= $mail->getSubject() ?></td>
        <td><?= $mail->fromName ? $mail->fromName : $mail->from ?></td>
        <td><a href="/mail/<?= $token ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('regular-view') ?></a></td>
        <td class="<?= $opened ? 'text-cyan' : 'text-muted' ?>"><i class="fa fa-<?= $mail->sent ? 'check' : 'times' ?>"></i></td>
    </tr>
  <?php endforeach ?>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>

<?php else: ?>
    <blockquote><?= $this->text('dashboard-mails-no-mails') ?></blockquote>
<?php endif ?>
  </div>
</div>

<?php $this->replace() ?>

