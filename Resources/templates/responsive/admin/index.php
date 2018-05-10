<?php $this->layout('admin/layout');

$sidebar = $this->raw('sidebar');
$this->section('admin-content');
?>

<div class="admin-content">
    <div class="inner-container">
        <h2><?= $this->text('admin-home-title') ?></h2>

        <?= $this->insert('admin/partials/typeahead', ['engines' => ['channel', 'call', 'project', 'user', 'consultant']]) ?>

    <?php foreach($sidebar as $item): ?>
        <hr>
        <h4><?= $item['text'] ?></h4>
        <div class="index-row">
            <?php foreach($item['submenu'] as $sub): ?>
            <div class="col-xs-6 col-sm-4"><a class="btn btn-default btn-block" href="<?= $sub['link'] ?>"><?= $sub['text'] ?></a></div>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>

    </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">

$('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
    if(datum && datum.url) location = datum.url;
});

</script>
<?php $this->append() ?>
