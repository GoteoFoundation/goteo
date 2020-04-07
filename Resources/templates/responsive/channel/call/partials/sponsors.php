<?php $channel=$this->channel; ?>

<?php if($channel->getSponsors()): ?>

<?php  
$main_sponsors=[];
$secondary_sponsors=[];
foreach ($channel->getSponsors() as $sponsor):
        if(!$sponsor->label)
          $main_sponsors[]=$sponsor;
        else
          $secondary_sponsors=$sponsor;
endforeach; 

?>

<div class="section sponsors">
  <div class="container">
    <h2 class="title"><span class="icon icon-rocket icon-3x"></span>Nuestros impulsores y colaboradores</h2>
    <ul class="img-list list-inline text-center">
    
   <ul class="list-inline text-left main-sponsors">
      <?php foreach ($main_sponsors as $sponsor): ?>
        <?php $sponsor_image=$sponsor->getImage(); ?>
        <li>
          <img src="<?= $sponsor_image->getLink(150, 80, false) ?>" >
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="row">

        <?php foreach($this->secondary_sponsors as $sponsor): ?>



        <?php endforeach; ?>

    </div>
  </div>

</div>

<?php endif; ?>