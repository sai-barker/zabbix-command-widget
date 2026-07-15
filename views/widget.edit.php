<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$form = new CWidgetFormView($data);

$form->addField(
	new CWidgetFieldMultiSelectHostView($data['fields']['hostid'])
);

for ($index = 1; $index <= 6; $index++) {
	$prefix = $index === 1 ? 'command_' : 'command_'.$index.'_';

	$form->addFieldset(
		(new CWidgetFormFieldsetCollapsibleView(_s('Button %1$d', $index)))
			->addField(new CWidgetFieldSelectView($data['fields'][$prefix.'scriptid']))
			->addField(new CWidgetFieldTextBoxView($data['fields'][$prefix.'label']))
			->addField(new CWidgetFieldColorView($data['fields'][$prefix.'color']))
			->addField(new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'width']))
			->addField(new CWidgetFieldIntegerBoxView($data['fields'][$prefix.'height']))
			->addField(new CWidgetFieldTextAreaView($data['fields'][$prefix.'manualinput']))
	);
}

$form
	->addFieldset(
		(new CWidgetFormFieldsetCollapsibleView(_('Layout')))
			->addField(new CWidgetFieldSelectView($data['fields']['layout_columns']))
			->addField(new CWidgetFieldSelectView($data['fields']['button_alignment']))
			->addField(new CWidgetFieldSelectView($data['fields']['layout_spacing']))
	)
	->addField(new CWidgetFieldCheckBoxView($data['fields']['show_details']))
	->includeJsFile('widget.edit.js.php')
	->show();
