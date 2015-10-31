<?php
/**
 * EMThemes
 *
 * @license commercial software
 * @copyright (c) 2012 Codespot Software JSC - EMThemes.com. (http://www.emthemes.com)
 */


function replacer($m) {
	global $vars;
	$prefix = @$vars[$m[1].':prefix'];
	$suffix = @$vars[$m[1].':suffix'];
	if(isset($vars[$m[1]])){
		$val = $vars[$m[1]] ? $vars[$m[1]] : $m[2];
		//return "$prefix $val $suffix";
		return $prefix . $val . $suffix;
	}
	return htmlspecialchars($prefix.$m[2].$suffix);
}

function _include($fn) {
	return include($fn);
}

$vars = $_GET;

$css = file_get_contents('theme.css');
if(isset($_GET['additional_css_file'])){
	$files = explode(',', $_GET['additional_css_file']);
	foreach ($files as $fn) {
		$custom = basename(trim($fn), '.css');
		if ($custom && file_exists("$custom.css.php")) $vars = array_merge(_include("$custom.css.php"), $vars);
		if ($custom && file_exists("$custom.css")) $css .= "\n".file_get_contents("$custom.css");
	}
}

$css = preg_replace_callback('/\/\*BEGIN:([a-zA-Z0-9_]+)\*\/(.*)\/\*END:(\1)\*\//U', 'replacer', $css);

# relace rgba to rgb on IE8
if (isset($vars['ie']) && $vars['ie'])
	$css = preg_replace('/rgba\(([0-9. ]*),([0-9. ]*),([0-9. ]*),([0-9. ]*)\)/i', 'rgb($1,$2,$3)', $css);

header('Content-Type: text/css');
echo $css;