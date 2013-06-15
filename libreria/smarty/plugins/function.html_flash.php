<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_html_flash($params, &$smarty)
{
	$ancho = isset($params['ancho']) && is_numeric($params['ancho']) ? $params['ancho'] : 0;
	$alto = isset($params['alto']) && is_numeric($params['alto']) ? $params['alto'] : 0;
	$url = isset($params['url']) ? $params['url'] : '';
	$html = '
		<object width="'.$ancho.'" height="'.$alto.'">
			<param name="movie" value="'.$url.'"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="'.$url.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$ancho.'" height="'.$alto.'">
			</embed>
		</object>';
	
	return $html;
}

/* vim: set expandtab: */

?>
