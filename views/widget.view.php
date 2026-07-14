<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$button = (new CButton('execute', $data['button_label']))
	->addClass('js-command-widget-execute')
	->setAttribute('data-hostid', (string) $data['hostid'])
	->setAttribute('data-scriptid', (string) $data['scriptid']);

$result = (new CDiv())
	->addClass('js-command-widget-result')
	->setAttribute('aria-live', 'polite');

(new CWidgetView($data))
	->addItem([
		new CDiv('Host: '.$data['hostname']),
		new CDiv('Script: '.$data['script_name']),
		$button,
		$result
	])
	->show();