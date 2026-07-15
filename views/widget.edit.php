<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

use Modules\ZabbixCommandWidget\Includes\WidgetForm;

$form = new CWidgetFormView($data);

$make_mini_field = static function(CWidgetFieldView $field, string $suffix = ''): CDiv {
	$control = [$field->getView()];

	if ($suffix !== '') {
		$control[] = new CSpan($suffix);
	}

	return (new CDiv([
		$field->getLabel(),
		(new CDiv($control))->addClass('zcw-edit-control')
	]))->addClass('zcw-edit-mini-field');
};

$make_checkbox = static function(CWidgetFieldView $field): CDiv {
	return (new CDiv([
		$field->getView(),
		$field->getLabel()
	]))->addClass('zcw-edit-checkbox');
};

$form->addField(
	new CWidgetFieldMultiSelectHostView($data['fields']['hostid'])
);

$form->addFieldset(
	(new CWidgetFormFieldsetCollapsibleView(_('Layout')))
		->setExpanded()
		->addFieldsGroup(
			(new CWidgetFieldsGroupView(_('Button layout')))
				->addField(new CWidgetFieldIntegerBoxView($data['fields']['button_count']))
				->addField(new CWidgetFieldSelectView($data['fields']['layout_columns']))
				->addField(new CWidgetFieldSelectView($data['fields']['button_alignment']))
				->addField(new CWidgetFieldSelectView($data['fields']['layout_spacing']))
				->addField(new CWidgetFieldCheckBoxView($data['fields']['show_details']))
		)
);

$button_count = max(1, min(
	WidgetForm::MAX_BUTTON_COUNT,
	(int) $data['fields']['button_count']->getValue()
));

for ($index = 1; $index <= WidgetForm::MAX_BUTTON_COUNT; $index++) {
	$prefix = $index === 1 ? 'command_' : 'command_'.$index.'_';

	$button_color = $form->registerField(new CWidgetFieldColorView($data['fields'][$prefix.'color']));
	$button_width = $form->registerField(new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'width']));
	$button_height = $form->registerField(new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'height']));
	$label_size = $form->registerField(new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'label_size']));

	$appearance_grid = (new CDiv([
		$make_mini_field($button_color),
		$make_mini_field($button_width, '%'),
		$make_mini_field($button_height, '%'),
		$make_mini_field($label_size, '%')
	]))
		->addClass('zcw-edit-grid')
		->addClass('zcw-edit-grid-four');

	$button_appearance = (new CWidgetFieldsGroupView(_('Button appearance')))
		->addField(new CWidgetFieldTextBoxView($data['fields'][$prefix.'label']))
		->addItem($appearance_grid);

	$description_size = $form->registerField(
		new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'description_size'])
	);
	$description_position = $form->registerField(
		new CWidgetFieldSelectView($data['fields'][$prefix.'description_position'])
	);
	$description_alignment = $form->registerField(
		new CWidgetFieldSelectView($data['fields'][$prefix.'description_alignment'])
	);
	$description_bold = $form->registerField(
		new CWidgetFieldCheckBoxView($data['fields'][$prefix.'description_bold'])
	);
	$description_italic = $form->registerField(
		new CWidgetFieldCheckBoxView($data['fields'][$prefix.'description_italic'])
	);
	$description_underline = $form->registerField(
		new CWidgetFieldCheckBoxView($data['fields'][$prefix.'description_underline'])
	);

	$description_layout = (new CDiv([
		$make_mini_field($description_size, '%'),
		$make_mini_field($description_position),
		$make_mini_field($description_alignment)
	]))
		->addClass('zcw-edit-grid')
		->addClass('zcw-edit-grid-three');

	$font_style = (new CDiv([
		new CLabel(_('Font style')),
		(new CDiv([
			$make_checkbox($description_bold),
			$make_checkbox($description_italic),
			$make_checkbox($description_underline)
		]))->addClass('zcw-edit-checkboxes')
	]))->addClass('zcw-edit-font-style');

	$description = (new CWidgetFieldsGroupView(_('Description')))
		->addField(
			(new CWidgetFieldTextAreaView($data['fields'][$prefix.'description']))->removeLabel()
		)
		->addItem($description_layout)
		->addItem($font_style);

	$button_fieldset = (new CWidgetFormFieldsetCollapsibleView(_s('Button %1$d', $index)))
			->addField(new CWidgetFieldSelectView($data['fields'][$prefix.'scriptid']))
			->addField(new CWidgetFieldTextAreaView($data['fields'][$prefix.'manualinput']))
			->addFieldsGroup($button_appearance)
			->addFieldsGroup($description);

	$button_fieldset->setId('zcw-button-fieldset-'.$index);

	if ($index > $button_count) {
		$button_fieldset->addStyle('display: none;');
	}

	$form->addFieldset($button_fieldset);
}

$form
	->includeJsFile('widget.edit.js.php')
	->show();
