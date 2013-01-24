<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];

$cuantos = $call->getSupporters(true);
$supporters = $call->getSupporters();
?>
<p><?php echo Text::get('call-header-supporters', $cuantos) ?></p>
<ul id="supporters">
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara4.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara5.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara2.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara1.jpg" alt="supporter" /></a></li>
	<li><a href="#"><img src="/data/images/cara3.jpg" alt="supporter" /></a></li>
	
</ul>
<?php # echo \trace($supporters); ?>