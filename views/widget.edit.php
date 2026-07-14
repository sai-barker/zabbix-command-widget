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


/**
 * Gauge chart widget form view.
 *
 * @var CView $this
 * @var array $data
 */

use Modules\LessonGaugeChart\Includes\WidgetForm;

$form = new CWidgetFormView($data);

$lefty_units = $form->registerField(new CWidgetFieldSelectView($data['fields']['value_units']));
$lefty_static_units = $form->registerField(
	(new CWidgetFieldTextBoxView($data['fields']['value_static_units']))
		->setPlaceholder(_('value'))
		->setWidth(ZBX_TEXTAREA_TINY_WIDTH)
);

$form
	->addField(
		(new CWidgetFieldMultiSelectItemView($data['fields']['itemid']))->setPopupParameter('numeric', true)
	)
	->addFieldset(
		(new CWidgetFormFieldsetCollapsibleView(_('Advanced configuration')))
			->addField(
				new CWidgetFieldColorView($data['fields']['chart_color'])
			)
			->addField(
				new CWidgetFieldNumericBoxView($data['fields']['value_min'])
			)
			->addField(
				new CWidgetFieldNumericBoxView($data['fields']['value_max'])
			)
			->addItem([
				$lefty_units->getLabel(),
				(new CFormField([
					$lefty_units->getView()->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
					$lefty_static_units->getView()
				]))
			])
			->addField(
				new CWidgetFieldTextBoxView($data['fields']['description'])
			)
	)
	->includeJsFile('widget.edit.js.php')
	->addJavaScript('widget_lesson_gauge_chart_form.init('.json_encode([
		'color_palette' => WidgetForm::DEFAULT_COLOR_PALETTE
	]).');')
	->show();
