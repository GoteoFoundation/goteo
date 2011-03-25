<?php
	if (isset($errors) && is_array($errors) && !empty($errors)) {
		foreach ($errors as $k=>$error) {
			echo '<span syle="color:red">' . $error . '</span><br />';
		}
	} elseif (isset($success) && is_array($success) && !empty($success)) {
		foreach ($success as $k=>$succ) {
			echo '<span syle="color:green">' . $succ . '</span><br />';
		}
	}
?>