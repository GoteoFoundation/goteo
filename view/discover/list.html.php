<html>
    <table border="1">
        <tr>
            <th>Proyecto</th>
            <th>Url</th>
        </tr>
        <?php foreach ($this['list'] as $id=>$name) : ?>
        <tr>
            <?php echo "<td>{$name}</td><td>".SITE_URL."/project/{$id}</td>"; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</html>
