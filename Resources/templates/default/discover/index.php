<?php

$this->layout("layout", [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover'),
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');
?>

        <div id="sub-header">
            <div>
                <h2><?= $this->text('discover-banner-header') ?></h2>
            </div>
        </div>

        <div id="main">
            <?= $this->insert('discover/partials/searcher') ?>

		<?php foreach ($this->lists as $type => $list) :
            if (!$list)
                continue;
            ?>
            <div class="widget projects">
                <h2 class="title"><?= $this->text('discover-group-'.$type.'-header') ?></h2>
                <?php foreach ($list as $group=>$projects) : ?>
                    <div class="discover-group discover-group-<?php echo $type ?>" id="discover-group-<?php echo $type ?>-<?php echo $group ?>">

                        <div class="discover-arrow-left">
                            <a class="discover-arrow" href="/discover/view/<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo $type.'-'.$projects['prev'] ?>">&nbsp;</a>
                        </div>

                        <?php foreach ($projects['items'] as $project) : ?>
                            <?= $this->insert('project/widget/project', ['project' => $project]) ?>
                        <?php endforeach ?>

                        <div class="discover-arrow-right">
                            <a class="discover-arrow" href="/discover/view/<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo $type.'-'.$projects['next'] ?>">&nbsp;</a>
                        </div>

                    </div>
                <?php endforeach ?>


                <!-- carrusel de imagenes -->
                <div class="navi-bar">
                    <ul class="navi">
                        <?php foreach (array_keys($list) as $group) : ?>
                        <li><a id="navi-discover-group-<?php echo $type.'-'.$group ?>" href="/discover/view/<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo "{$type}-{$group}" ?>" class="navi-discover-group navi-discover-group-<?php echo $type ?>"><?php echo $group ?></a></li>
                        <?php endforeach ?>
                    </ul>
                    <a class="all" href="/discover/view/<?php echo $type; ?>"><?= $this->text('regular-see_all') ?></a>
                </div>

            </div>

        <?php endforeach ?>

        </div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
        jQuery(document).ready(function ($) {
            /* todo esto para cada tipo de grupo */
            <?php foreach ($this->lists as $type=>$list) :
                if(array_empty($list)) continue; ?>
                $("#discover-group-<?php echo $type ?>-1").show();
                $("#navi-discover-group-<?php echo $type ?>-1").addClass('active');
            <?php endforeach ?>

            $(".discover-arrow").click(function (event) {
                event.preventDefault();

                /* Quitar todos los active, ocultar todos los elementos */
                $(".navi-discover-group-"+this.rev).removeClass('active');
                $(".discover-group-"+this.rev).hide();
                /* Poner acctive a este, mostrar este */
                $("#navi-discover-group-"+this.rel).addClass('active');
                $("#discover-group-"+this.rel).show();
            });

            $(".navi-discover-group").click(function (event) {
                event.preventDefault();

                /* Quitar todos los active, ocultar todos los elementos */
                $(".navi-discover-group-"+this.rev).removeClass('active');
                $(".discover-group-"+this.rev).hide();
                /* Poner acctive a este, mostrar este */
                $("#navi-discover-group-"+this.rel).addClass('active');
                $("#discover-group-"+this.rel).show();
            });
        });
    // @license-end
    </script>

<?php $this->append() ?>
