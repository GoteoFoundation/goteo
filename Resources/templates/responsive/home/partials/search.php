<div class="section search" >
	<div class="container">
		<form>
			<div class="row" >
				<div class="col-md-12 text-center" >
					<div class="inner-addon left-addon">
    					<i class="glyphicon glyphicon-search"></i>
    					<input type="text" name="keyword" placeholder="<?= $this->text('home-search-keyword') ?>" >
					</div>
					<div class="inner-addon left-addon">
    					<i class="glyphicon glyphicon-map-marker"></i>
						<input type="text" name="location" placeholder="<?= $this->text('home-search-location') ?>" >
					</div>
					<button type="submit" class="btn btn-light-green" ><?= $this->text('regular-search') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>