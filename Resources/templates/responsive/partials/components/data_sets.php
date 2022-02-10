<div id="data-sets" class="section data-sets">
    <div class="container">
        <div class="dataset_list">
            <?php foreach ($this->dataSets as $dataSet): ?>
                <div class="item">
                    <div class="content">
                        <h2 class="title"><?= $dataSet->getTitle() ?></h2>
                        <div class="description">
                            <h3 class="type"><?= $this->t("data-set-type-{$dataSet->getType()}") ?></h3>
                            <p><?= $dataSet->getDescription() ?></p>
                        </div>
                        <div class="date-info">
                            <div class="date-created">
                                <span><?= $this->text('data-set-created-at', date_format($dataSet->getModifiedAt(),"d-m-Y")) ?></span>
                            </div>
                            <div class="date-modified">
                                <span><?= $this->text('data-set-modified-at', date_format($dataSet->getModifiedAt(),"d-m-Y")) ?></span>
                            </div>
                        </div>
                        <a href="//<?= $dataSet->getUrl() ?>" class="btn btn-transparent pull-right" download>
                            <i class="icon icon-pdf"></i><?= $this->text('regular-download').' .CSV' ?>
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
