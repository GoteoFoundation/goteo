<?php

$rewards = $this->rewards;
$locations = $this->locations;
$categories = $this->categories;
$params = $this->params;

?>

<div class="section main-info" >
    <div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="title">
					<?=$this->text('discover-results-header')?>
				</h2>
	            <div class="custom-search-input">
	                <div class="input-group col-md-12">
	                	<form method="post" action="/discover/results">
		                    <input id="text-query" type="text" name="query" class="search-query form-control" placeholder="Search" value="<?= $params['query'] ?>" />
		                    <button type="submit" class="btn btn-cyan"><?= $this->text('regular-search') ?></button>
		                </form>
	                </div>
	            </div>
	        </div>
		</div>
	</div>


</div>
