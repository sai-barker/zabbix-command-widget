<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$get_button_colors = static function(string $color): array {
	$color = strtoupper($color);

	if (!preg_match('/^[0-9A-F]{6}$/', $color)) {
		$color = '0275B8';
	}

	$red = hexdec(substr($color, 0, 2));
	$green = hexdec(substr($color, 2, 2));
	$blue = hexdec(substr($color, 4, 2));
	$brightness = (299 * $red + 587 * $green + 114 * $blue) / 1000;

	return ['#'.$color, $brightness > 160 ? '#1f2d3d' : '#ffffff'];
};

$content = (new CDiv())->addClass('zcw-content');
$alignment_classes = ['zcw-align-left', 'zcw-align-center', 'zcw-align-right'];
$spacing_classes = ['zcw-spacing-compact', 'zcw-spacing-normal', 'zcw-spacing-relaxed'];

if ($data['show_details']) {
	$details = [new CDiv('Host: '.$data['hostname'])];

	foreach ($data['commands'] as $command) {
		$details[] = new CDiv(_s('Button %1$d script: %2$s', $command['index'], $command['script_name']));
	}

	$content->addItem((new CDiv($details))->addClass('zcw-details'));
}

$command_items = [];

foreach ($data['commands'] as $command) {
	[$button_color, $text_color] = $get_button_colors($command['color']);
	$button_height = max(18, (int) round(36 * $command['height'] / 100));

	$button = (new CButton('execute_'.$command['index'], $command['label']))
		->addClass('js-command-widget-execute')
		->addClass('zcw-command-button')
		->addStyle(
			'--zcw-button-color: '.$button_color.'; '
			.'--zcw-button-text-color: '.$text_color.'; '
			.'--zcw-button-height: '.$button_height.'px;'
		)
		->setAttribute('data-hostid', (string) $data['hostid'])
		->setAttribute('data-scriptid', (string) $command['scriptid'])
		->setAttribute('data-manualinput', $command['manualinput'])
		->setAttribute('data-manualinput-enabled', $command['manualinput_enabled'] ? '1' : '0')
		->setAttribute('data-confirmation', $command['confirmation']);

	$result = (new CDiv())
		->addClass('js-command-widget-result')
		->addClass('zcw-result')
		->setAttribute('aria-live', 'polite');

	$command_items[] = (new CDiv([
		(new CDiv($button))
			->addClass('zcw-actions')
			->addClass($alignment_classes[$data['button_alignment']])
			->addStyle('--zcw-button-width: '.$command['width'].'%;'),
		$result
	]))->addClass('zcw-command');
}

$content->addItem(
	(new CDiv($command_items))
		->addClass('zcw-command-list')
		->addClass($spacing_classes[$data['layout_spacing']])
		->addStyle('--zcw-columns: '.$data['layout_columns'].';')
);

(new CWidgetView($data))
	->addItem($content)
	->show();
