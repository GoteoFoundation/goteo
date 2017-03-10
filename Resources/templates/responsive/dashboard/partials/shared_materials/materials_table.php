 <table class="table social-commitment table-striped">
    <thead>
        <tr>
            <th></th>
            <th><?= $this->text('regular-license') ?></th>
            <th></th>
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
                        <button data-toggle="modal" data-target="#UrlModal" data-value="<?= $reward->url ?>" data-reward-id="<?= $reward->id ?>" class="btn green edit-url" ><?= $this->text('dashboard-edit-share-material-url') ?></button>
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




