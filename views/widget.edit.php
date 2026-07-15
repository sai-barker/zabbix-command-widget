<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

(new CWidgetFormView($data))
	->addField(
		new CWidgetFieldMultiSelectHostView($data['fields']['hostid'])
	)
	->addField(
		new CWidgetFieldSelectView($data['fields']['command_scriptid'])
	)
	->addField(
		new CWidgetFieldTextBoxView($data['fields']['command_label'])
	)
	->addField(
		new CWidgetFieldColorView($data['fields']['command_color'])
	)
	->addField(
		new CWidgetFieldTextAreaView($data['fields']['command_manualinput'])
	)
	->addField(
		new CWidgetFieldCheckBoxView($data['fields']['show_details'])
	)
	->includeJsFile('widget.edit.js.php')
	->show();
