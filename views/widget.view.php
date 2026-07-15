<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$button_color = strtoupper($data['button_color']);

if (!preg_match('/^[0-9A-F]{6}$/', $button_color)) {
	$button_color = '0275B8';
}

$red = hexdec(substr($button_color, 0, 2));
$green = hexdec(substr($button_color, 2, 2));
$blue = hexdec(substr($button_color, 4, 2));
$brightness = (299 * $red + 587 * $green + 114 * $blue) / 1000;
$button_text_color = $brightness > 160 ? '#1f2d3d' : '#ffffff';

$button = (new CButton('execute', $data['button_label']))
	->addClass('js-command-widget-execute')
	->addClass('zcw-command-button')
	->addStyle('--zcw-button-color: #'.$button_color.'; --zcw-button-text-color: '.$button_text_color.';')
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
