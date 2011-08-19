<?php

use Goteo\Library\Text;

?>
<div class="widget">
    <form id="filter-form" action="/admin/invests/add" method="post">
        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="" />
        </p>
        <p>
            <label for="invest-user">Usuario:</label><br />
            <select id="invest-user" name="user">
                <option value="">Seleccionar usuario que hace el aporte</option>
            <?php foreach ($this['users'] as $userId=>$userName) : ?>
                <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="invest-project">Proyecto:</label><br />
            <select id="invest-project" name="project">
                <option value="">Seleccionar el proyecto al que se aporta</option>
            <?php foreach ($this['projects'] as $projectId=>$projectName) : ?>
                <option value="<?php echo $projectId; ?>"><?php echo $projectName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="invest-campaign">Campaña:</label><br />
            <select id="invest-campaign" name="campaign">
                <option value="">Seleccionar la campaña a la que se asigna este aporte</option>
            <?php foreach ($this['campaigns'] as $campaignId=>$campaignName) : ?>
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