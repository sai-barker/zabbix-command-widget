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
	->show();