<?php

use Goteo\Library\Text;

$amount = (isset($_GET['amount'])) ? $_GET['amount'] : null;
$user   = (isset($_GET['user'])) ? $_GET['user'] : null;
$proj   = (isset($_GET['proj'])) ? $_GET['proj'] : null;

?>
<div class="widget">
    <form id="filter-form" action="/admin/accounts/add" method="post">
        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="<?php echo $amount; ?>" />
        </p>
        <p>
            <label for="invest-user">Usuario:</label><br />
            <select id="invest-user" name="user">
                <option value="">Seleccionar usuario que hace el aporte</option>
            <?php foreach ($this['users'] as $userId=>$userName) : 
                $selected = $userId == $user ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $userId; ?>"<?php echo $selected; ?>><?php echo $userId . ' :: ' . substr($userName, 0, 20); ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="invest-project">Proyecto:</label><br />
            <select id="invest-project" name="project">
                <option value="">Seleccionar el proyecto al que se aporta</option>
            <?php foreach ($this['projects'] as $projectId=>$projectName) : 
                $selected = $projectId == $proj ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $projectId; ?>"<?php echo $selected; ?>><?php echo $projectName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="invest-campaign">Convocatoria:</label><br />
            <select id="invest-campaign" name="campaign">
                <option value="">Seleccionar la convocatoria que riega este aporte</option>
            <?php foreach ($this['calls'] as $campaignId=>$campaignName) : ?>
                <option value="<?php echo $campaignId; ?>"><?php echo $campaignName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="invest-anonymous">Aporte anónimo:</label><br />
            <input id="invest-anonymous" type="checkbox" name="anonymous" value="1">
        </p>

        <input type="submit" name="add" value="Generar aporte" />

    </form>
</div>