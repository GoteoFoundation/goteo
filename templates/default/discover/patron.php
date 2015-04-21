<?php

use Goteo\Util\Pagination\Paginated;
use Goteo\Util\Pagination\DoubleBarLayout;

$this->layout("layout", [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover') . ' - ' . strip_tags($this->raw('title')),
    'meta_description' => $this->text('meta-description-discover'),
    ]);


$pagedResults = new Paginated($this->list, 9, isset($_GET['page']) ? $_GET['page'] : 1);

$this->section('content');
?>


        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->raw('title')?></h2>
            </div>
        </div>

        <div id="main">

            <div class="widget projects">
                <?php while ($project = $pagedResults->fetchPagedRow()) : ?>
                    <?= $this->insert('project/widget/project', ['project' => $project]) ?>
                <?php endwhile ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>

<?php $this->replace() ?>
