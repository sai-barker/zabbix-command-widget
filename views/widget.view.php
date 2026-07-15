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
	->addClass('zcw-result')
	->setAttribute('aria-live', 'polite');

$content = (new CDiv())->addClass('zcw-content');

if ($data['show_details']) {
	$content->addItem(
		(new CDiv([
			new CDiv('Host: '.$data['hostname']),
			new CDiv('Script: '.$data['script_name'])
		]))->addClass('zcw-details')
	);
}

$content->addItem(
	(new CDiv($button))->addClass('zcw-actions')
);
$content->addItem($result);

(new CWidgetView($data))
	->addItem($content)
	->show();
