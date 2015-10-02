    <p>
        <label for="node-name">Nombre:</label><br />
        <input type="text" id="node-name" name="name" value="<?= $this->node->name; ?>" style="width:250px" />
    </p>
    <p>
        <label for="node-email">Email Institucional:</label><br />
        <input type="text" id="node-email" name="email" value="<?= $this->node->email; ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-consultant">Asesor por defecto (Para proyectos del canal):<br />

        </label>
        <select id="node-consultant" name="default_consultant">
            <option value="">Sin asesor</option>
            <?php foreach ($this->node_admins as $userId => $userName) : ?>
                <option value="<?= $userId; ?>" <?= ($this->node->default_consultant == $userId)? "selected" : '' ?>><?= $userName; ?></option>
            <?php endforeach; ?>
        </select>
    </p>

<?php if(!$this->masternode): ?>
    <p>
        <label for="node-sponsors">Límite sponsors:</label><br />
        <input type="text" id="node-sponsors" name="sponsors_limit" value="<?= (int) $this->node->sponsors_limit ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-sponsors">URL (dejar vacio para <b>/channel/<?= $this->node->id ?>)</b>:</label><br />
        <input type="text" id="node-url" name="url" value="<?= $this->node->url; ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-active">Activo:</label><br />
        <input type="checkbox" id="node-active" name="active" value="1" <?php if ($this->node->active) echo ' checked="checked"'; ?>/>
    </p>

<?php endif ?>
