<?php declare(strict_types = 0);
/*
** Zabbix
** Copyright (C) 2001-2023 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


use Modules\LessonGaugeChart\Widget;

?>

window.widget_lesson_gauge_chart_form = new class {

	init({color_palette}) {
		this._unit_select = document.getElementById('value_units');
		this._unit_value = document.getElementById('value_static_units');

		this._unit_select.addEventListener('change', () => this.updateForm());

		colorPalette.setThemeColors(color_palette);

		for (const colorpicker of jQuery('.<?= ZBX_STYLE_COLOR_PICKER ?> input')) {
			jQuery(colorpicker).colorpicker();
		}

		const overlay = overlays_stack.getById('widget_properties');

		for (const event of ['overlay.reload', 'overlay.close']) {
			overlay.$dialogue[0].addEventListener(event, () => { jQuery.colorpicker('hide'); });
		}

		this.updateForm();
	}

	updateForm() {
		this._unit_value.disabled = this._unit_select.value == <?= Widget::UNIT_AUTO ?>;
	}
};
