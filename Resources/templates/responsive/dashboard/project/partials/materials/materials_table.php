 <table class="table spacer social-commitment">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?= $this->text('regular-license') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody id="materials-table">
        <?php foreach($this->project->social_rewards as $reward): ?>
            <tr>
                <td>
                    <strong><?= $reward->reward ?></strong>
                    <p>
                        <?= $reward->description ?>
                    </p>
                    <p>
                        <button data-toggle="modal" data-target="#UrlModal" data-url="<?= $reward->url ?>" data-reward="<?= $reward->id ?>" class="btn btn-default edit-url" ><i class="fa fa-link"></i> <?= $this->text('dashboard-edit-share-material-url') ?></button>
                        <?= $reward->url ?>
                    </p>
                </td>
                <td>
                    <?= $this->licenses_list[$reward->license] ?>
                </td>
                <td>
                    <?php if($reward->bonus): ?>
                        <!--<span class="glyphicon glyphicon-edit"></span>-->
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>




