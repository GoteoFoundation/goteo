<?php
	if (isset($errors) && is_array($errors) && !empty($errors)) {
		foreach ($errors as $k=>$error) {
			echo '<p style="color:red">' . $error . '</p>';
		}
	} elseif (isset($success) && is_array($success) && !empty($success)) {
		foreach ($success as $k=>$succ) {
			echo '<p style="color:green">' . $succ . '</p>';
		}
	}
?>