<?php

use Goteo\Core\View,
    Goteo\Model\Call,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$this->layout("layout", [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover-calls'),
    'meta_description' => $this->text('meta-description-discover'),
    'image' => $og_image
    ]);

$this->section('content');


$calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
$campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
$success = Call::getActive(5); // convocatorias en modalidad 2; repartiendo capital riego

// en la página de cofinanciadores, paginación de 20 en 20
$pagedResults = new Paginated($this->list, 9, isset($_GET['page']) ? $_GET['page'] : 1);

$this->section('content');
?>


        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->raw('title')?></h2>
            </div>
        </div>

        <div id="main">

            <div class="widget calls">

                <div class="title">
                    <div class="logo"><?=$this->text('home-calls-header')?></div>
                    <?php if (!empty($calls)) : ?>
                    <div class="call-count mod1">
                        <strong><?php echo count($calls) ?></strong>
                        <span>Convocatorias<br />abiertas</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($campaigns)) : ?>
                    <div class="call-count mod2">
                        <strong><?php echo count($campaigns) ?></strong>
                        <span>Campañas<br />activas</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success)) : ?>
                    <div class="call-count mod3" style="margin-right: 0px;">
                        <strong><?php echo count($success) ?></strong>
                        <span>Convocatorias<br />exitosas</span>
                    </div>
                    <?php endif; ?>
                </div>

            <?php while ($call = $pagedResults->fetchPagedRow()) {
                echo View::get('call/widget/call.html.php', array('call' => $call));
            } ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>

<?php $this->replace() ?>
