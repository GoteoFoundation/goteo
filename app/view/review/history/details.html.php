<?php
use Goteo\Core\View,
    Goteo\Model\Criteria;

$review       = $vars['review'];
$evaluation   = $vars['evaluation'];

$sections = Criteria::sections();
$criteria = array();
foreach ($sections as $sectionId=>$sectionName) {
    $criteria[$sectionId] = Criteria::getAll($sectionId);
}

?>
<h2 class="title">Mi revisión anterior</h2>
<div class="widget">
    <p>Del proyecto <strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
    <p>La edición del proyecto alcanzó el <strong><?php echo $review->progress; ?>%</strong>, la puntuación de tu revisión fue de <span id="total-score"><?php echo $evaluation['score'] . '/' . $evaluation['max']; ?></span></p>
</div>
<?php foreach ($sections as $sectionId=>$sectionName) : ?>
<div class="widget">
    <h2 class="title"><?php echo $sectionName; ?></h2>
    <p>
        Otrogaste puntos porque:<br />
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
        Las mejoras que propusiste <?php echo strtolower($sectionName); ?>:<br />
        <blockquote><?php echo nl2br($evaluation[$sectionId]['recommendation']); ?></blockquote>
    </p>
</div>
<?php endforeach; ?>
