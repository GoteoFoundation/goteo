<?php

$scripts = [];
    foreach($this->intervals as $interval => $label): ?>
    <script type="text/template" id="template-matchfunding-global-<?= $interval ?>"><?=
        $this->insertIf("admin/stats/totals/partials/invests/" . $this->part, [
            'id' => 'global',
            'method' => 'global',
            'interval' => $interval
        ]) ?: '<p class="text-danger">Error loading [' . $this->part . '] view</p>'
        ?>
    </script>
<?php endforeach;



