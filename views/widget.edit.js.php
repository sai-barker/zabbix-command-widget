<?php declare(strict_types = 0);

/**
 * Initialise Zabbix 7.2 colour-picker controls in the widget edit form.
 */
?>

for (const colorpicker of jQuery('.<?= ZBX_STYLE_COLOR_PICKER ?> input')) {
	jQuery(colorpicker).colorpicker();
}

const overlay = overlays_stack.getById('widget_properties');

if (overlay) {
	for (const event of ['overlay.reload', 'overlay.close']) {
		overlay.$dialogue[0].addEventListener(event, () => {
			jQuery.colorpicker('hide');
		});
	}
}
