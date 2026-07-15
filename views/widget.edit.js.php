<?php declare(strict_types = 0);

/**
 * Initialise Zabbix 7.2 colour-picker controls in the widget edit form.
 */
?>

for (const colorpicker of jQuery('.<?= ZBX_STYLE_COLOR_PICKER ?> input')) {
	jQuery(colorpicker).colorpicker();
}

const manual_input = document.getElementById('command_manualinput');

if (manual_input) {
	manual_input.rows = 3;
	manual_input.style.height = 'auto';
	manual_input.style.minHeight = '72px';
	manual_input.style.resize = 'vertical';
}

const overlay = overlays_stack.getById('widget_properties');

if (overlay) {
	for (const event of ['overlay.reload', 'overlay.close']) {
		overlay.$dialogue[0].addEventListener(event, () => {
			jQuery.colorpicker('hide');
		});
	}
}
