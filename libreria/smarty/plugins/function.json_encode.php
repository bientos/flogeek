<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_checkboxes} function plugin
 *
 * File:       function.html_checkboxes.php<br>
 * Type:       function<br>
 * Name:       html_checkboxes<br>
 * Date:       24.Feb.2003<br>
 * Purpose:    Prints out a list of checkbox input types<br>
 * Input:<br>
 *           - name       (optional) - string default "checkbox"
 *           - values     (required) - array
 *           - options    (optional) - associative array
 *           - checked    (optional) - array default not set
 *           - separator  (optional) - ie <br> or &nbsp;
 *           - output     (optional) - the output next to each checkbox
 *           - assign     (optional) - assign the output as an array to this variable
 * Examples:
 * <pre>
 * {html_checkboxes values=$ids output=$names}
 * {html_checkboxes values=$ids name='box' separator='<br>' output=$names}
 * {html_checkboxes values=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * @link http://smarty.php.net/manual/en/language.function.html.checkboxes.php {html_checkboxes}
 *      (Smarty online manual)
 * @author     Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author credits to Monte Ohrt <monte at ohrt dot com>
 * @version    1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_json_encode($params, &$smarty)
{
	return json_encode($params['arreglo']);
}

