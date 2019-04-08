<html>
    <table border="1">
        <tr>
            <th>Proyecto</th>
            <th>Url</th>
        </tr>
        <?php foreach ($this->list as $id => $project) : ?>
        <tr>
            <?php echo "<td>{$this->ee($project->name)}</td><td>".SITE_URL."/project/{$project->id}</td>"; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</html>
