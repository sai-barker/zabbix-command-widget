<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

(new CWidgetView($data))
	->addItem([
		new CDiv('Host: '.$data['hostname']),
		new CDiv('Script: '.$data['script_name']),
		(new CButton('execute', _('Execute')))
			->addClass('js-command-widget-execute')
	])
	->show();