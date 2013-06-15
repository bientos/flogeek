<?php

function smarty_modifier_base64($string, $esc_type = 'html', $char_set = 'ISO-8859-1')
{
	return base64_encode($string);
}

/* vim: set expandtab: */

?>
