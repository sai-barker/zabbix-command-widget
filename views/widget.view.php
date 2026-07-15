<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$button = (new CButton('execute', $data['button_label']))
	->addClass('js-command-widget-execute')
	->setAttribute('data-hostid', (string) $data['hostid'])
	->setAttribute('data-scriptid', (string) $data['scriptid'])
	->setAttribute('data-manualinput', $data['manualinput'])
	->setAttribute(
		'data-manualinput-enabled',
		$data['manualinput_enabled'] ? '1' : '0'
	)
	->setAttribute('data-confirmation', $data['confirmation']);

$result = (new CDiv())
	->addClass('js-command-widget-result')
	->setAttribute('aria-live', 'polite');

$items = [];

if ($data['show_details']) {
	$items[] = new CDiv('Host: '.$data['hostname']);
	$items[] = new CDiv('Script: '.$data['script_name']);
}

$items[] = $button;
$items[] = $result;

(new CWidgetView($data))
	->addItem($items)
	->show();
