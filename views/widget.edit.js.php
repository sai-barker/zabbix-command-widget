<?php declare(strict_types = 0);

/**
 * Initialise Zabbix 7.2 colour-picker controls in the widget edit form.
 */
?>

for (const colorpicker of jQuery('.<?= ZBX_STYLE_COLOR_PICKER ?> input')) {
	jQuery(colorpicker).colorpicker();
}

for (const manual_input of document.querySelectorAll(
	'textarea[id^="command_"][id$="manualinput"]'
)) {
	manual_input.rows = 2;
	manual_input.style.height = 'auto';
	manual_input.style.minHeight = '54px';
	manual_input.style.resize = 'vertical';
}

for (const description of document.querySelectorAll(
	'textarea[id^="command_"][id$="description"]'
)) {
	description.rows = 2;
	description.style.height = 'auto';
	description.style.minHeight = '54px';
	description.style.resize = 'vertical';
}

const button_count = document.getElementById('button_count');

const update_button_fieldsets = () => {
	if (!button_count) {
		return;
	}

	const count = Math.max(1, Math.min(20, Number.parseInt(button_count.value, 10) || 1));

	for (let index = 1; index <= 20; index++) {
		const fieldset = document.getElementById(`zcw-button-fieldset-${index}`);

		if (fieldset) {
			fieldset.style.display = index <= count ? '' : 'none';
		}
	}
};

if (button_count) {
	button_count.addEventListener('input', update_button_fieldsets);
	button_count.addEventListener('change', update_button_fieldsets);
	update_button_fieldsets();
}

const overlay = overlays_stack.getById('widget_properties');

if (overlay) {
	for (const event of ['overlay.reload', 'overlay.close']) {
		overlay.$dialogue[0].addEventListener(event, () => {
			jQuery.colorpicker('hide');
		});
	}
}
