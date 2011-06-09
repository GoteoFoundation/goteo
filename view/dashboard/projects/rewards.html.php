		<p>
			Mis cofinanciadores:<br />
		<?php
		foreach ($this['investors'] as $user=>$investor) {
			echo "{$investor->avatar} {$investor->name} De nivel {$investor->worth}  Cofinancia {$investor->projects} proyectos  Me aporta: {$investor->amount} € <br />";
		}
		?>
		</p>
