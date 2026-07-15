<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {
	private const COMMAND_COUNT = 6;

	protected function doAction(): void {
		$hostid = $this->fields_values['hostid'][0] ?? null;
		$show_details = (int) ($this->fields_values['show_details'] ?? 0) === 1;
		$layout_columns = max(1, min(6, (int) ($this->fields_values['layout_columns'] ?? 2)));
		$button_alignment = max(0, min(2, (int) ($this->fields_values['button_alignment'] ?? 1)));
		$layout_spacing = max(0, min(2, (int) ($this->fields_values['layout_spacing'] ?? 1)));
		$hostname = _('Unknown host');

		if ($hostid !== null) {
			$hosts = API::Host()->get([
				'output' => ['name'],
				'hostids' => [$hostid]
			]);

			if ($hosts) {
				$hostname = $hosts[0]['name'];
			}
		}

		$scriptids = [];

		for ($index = 1; $index <= self::COMMAND_COUNT; $index++) {
			$scriptid = $this->fields_values[$this->getFieldName($index, 'scriptid')] ?? 0;

			if ($scriptid) {
				$scriptids[] = $scriptid;
			}
		}

		$scripts = $scriptids
			? API::Script()->get([
				'output' => ['name', 'manualinput', 'confirmation'],
				'scriptids' => $scriptids,
				'preservekeys' => true
			])
			: [];

		$commands = [];

		for ($index = 1; $index <= self::COMMAND_COUNT; $index++) {
			$scriptid = $this->fields_values[$this->getFieldName($index, 'scriptid')] ?? 0;

			if (!$scriptid || !array_key_exists($scriptid, $scripts)) {
				continue;
			}

			$script = $scripts[$scriptid];
			$label = trim((string) ($this->fields_values[$this->getFieldName($index, 'label')] ?? ''));

			if ($label === '') {
				$label = _s('Button %1$d', $index);
			}

			$commands[] = [
				'index' => $index,
				'scriptid' => $scriptid,
				'script_name' => $script['name'],
				'label' => $label,
				'color' => $this->fields_values[$this->getFieldName($index, 'color')] ?? '0275B8',
				'width' => max(10, min(100,
					(int) ($this->fields_values[$this->getFieldName($index, 'width')] ?? 100)
				)),
				'height' => max(50, min(300,
					(int) ($this->fields_values[$this->getFieldName($index, 'height')] ?? 100)
				)),
				'label_size' => max(50, min(300,
					(int) ($this->fields_values[$this->getFieldName($index, 'label_size')] ?? 100)
				)),
				'description' => trim((string) (
					$this->fields_values[$this->getFieldName($index, 'description')] ?? ''
				)),
				'description_position' => max(0, min(1, (int) (
					$this->fields_values[$this->getFieldName($index, 'description_position')] ?? 0
				))),
				'description_size' => max(50, min(300, (int) (
					$this->fields_values[$this->getFieldName($index, 'description_size')] ?? 100
				))),
				'description_alignment' => max(0, min(2, (int) (
					$this->fields_values[$this->getFieldName($index, 'description_alignment')] ?? 1
				))),
				'description_bold' => (int) (
					$this->fields_values[$this->getFieldName($index, 'description_bold')] ?? 0
				) === 1,
				'description_italic' => (int) (
					$this->fields_values[$this->getFieldName($index, 'description_italic')] ?? 0
				) === 1,
				'description_underline' => (int) (
					$this->fields_values[$this->getFieldName($index, 'description_underline')] ?? 0
				) === 1,
				'manualinput' => $this->fields_values[$this->getFieldName($index, 'manualinput')] ?? '',
				'manualinput_enabled' => (int) $script['manualinput'] === 1,
				'confirmation' => $script['confirmation'] ?? ''
			];
		}

		$this->setResponse(new CControllerResponseData([
			'name' => $this->getInput('name', $this->widget->getName()),
			'hostid' => $hostid,
			'hostname' => $hostname,
			'commands' => $commands,
			'layout_columns' => $layout_columns,
			'button_alignment' => $button_alignment,
			'layout_spacing' => $layout_spacing,
			'show_details' => $show_details,
			'user' => ['debug_mode' => $this->getDebugMode()]
		]));
	}

	private function getFieldName(int $index, string $field): string {
		return $index === 1 ? 'command_'.$field : 'command_'.$index.'_'.$field;
	}
}
