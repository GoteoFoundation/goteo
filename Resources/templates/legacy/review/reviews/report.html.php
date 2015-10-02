<?php
use Goteo\Core\View,
    Goteo\Model\Criteria;

echo View::get('review/reviews/selector.html.php', $vars);

$review   = $vars['review'];
$evaluation = $vars['evaluation'];

$sections = Criteria::sections();
$criteria = array();
foreach ($sections as $sectionId=>$sectionName) {
    $criteria[$sectionId] = Criteria::getAll($sectionId);
}

?>
<div class="widget">
    Puntuación de tu revisión: <span id="total-score"><?php echo $evaluation['score'] . '/' . $evaluation['max']; ?></span>
</div>
<?php foreach ($sections as $sectionId=>$sectionName) : ?>
<div class="widget">
    <h2 class="title"><?php echo $sectionName; ?></h2>
    <p>
        Otrogas puntos porque:<br />
        <blockquote>
        <?php foreach ($criteria[$sectionId] as $crit) :
            if ($evaluation['criteria'][$crit->id] > 0) echo '· ' . $crit->title . '<br />';
        endforeach; ?>
        </blockquote>
    </p>
    <p>
        Tu evaluación <?php echo strtolower($sectionName); ?>:<br />
        <blockquote><?php echo nl2br($evaluation[$sectionId]['evaluation']); ?></blockquote>
    </p>
    <p>
        Las mejoras que harías <?php echo strtolower($sectionName); ?>:<br />
        <blockquote><?php echo nl2br($evaluation[$sectionId]['recommendation']); ?></blockquote>
    </p>
</div>
<?php endforeach; ?>
