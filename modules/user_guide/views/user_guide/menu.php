<?php
/**
* Kohana User Guide Menu
* Topics in Categories (Eg, General, Libraries)
* Articles in Sections (Eg, Installation)
* Libraries and Helpers are in alphabetic order
* Other Categories are in logical order
*/
$menus = array
(
	'Kohana' => array
	(
		'About',
		'Requirements',
		'Downloads',
		'Installation',
		'Links'
	),
	'General' => array
	(
		'Definitions',
		'Bootstrapping',
		'Configuration',
		'Libraries',
		'Controllers',
		'Models',
		'Views',
		'Helpers'
	),
	'Libraries' => array
	(
		'Cache',
		'Controller',
		'Database',
		'Encryption',
		'Input',
		'Loader',
		'Model',
		'Pagination',
		'Router',
		'Session',
		'URI',
		'View'
	),
	'Helpers' => array
	(
		'File',
		'Html',
		'Text',
		'Url'
	)
);
?>
<ul>
<?php

foreach($menus as $category => $menu):

	$active = (strtolower($category) == $active_category) ? ' active' : '';

?>
<li class="first<?php echo $active ?>"><span><?php echo $category ?></span><ul>
<?php

	foreach($menu as $section):

		$active = (strtolower($section) == $active_section) ? 'lite' : '';

?>
<li class="<?php echo $active ?>"><?php echo html::anchor(strtolower('user_guide/'.$category.'/'.$section), $section) ?></li>
<?php

	endforeach;

?>
</ul></li>
<?php

endforeach;

?>
</ul>