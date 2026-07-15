<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {

	protected function doAction(): void {
		$hostid = $this->fields_values['hostid'][0] ?? null;
		$scriptid = $this->fields_values['command_scriptid'] ?? null;
		$button_label = trim($this->fields_values['command_label'] ?? '');
		$button_color = $this->fields_values['command_color'] ?? '0275B8';
		$manualinput = $this->fields_values['command_manualinput'] ?? '';
		$show_details = (int) ($this->fields_values['show_details'] ?? 0) === 1;

		if ($button_label === '') {
			$button_label = _('Execute');
		}

		$hostname = _('Unknown host');
		$script_name = _('No script selected');
		$manualinput_enabled = false;
		$confirmation = '';

		if ($hostid !== null) {
			$hosts = API::Host()->get([
				'output' => ['name'],
				'hostids' => [$hostid]
			]);

			if ($hosts) {
				$hostname = $hosts[0]['name'];
			}
		}

		if ($scriptid) {
			$scripts = API::Script()->get([
				'output' => [
					'name',
					'manualinput',
					'confirmation'
				],
				'scriptids' => [$scriptid]
			]);

			if ($scripts) {
				$script_name = $scripts[0]['name'];
				$manualinput_enabled = (int) $scripts[0]['manualinput'] === 1;
				$confirmation = $scripts[0]['confirmation'] ?? '';
			}
		}

		$this->setResponse(new CControllerResponseData([
			'name' => $this->getInput('name', $this->widget->getName()),
			'hostid' => $hostid,
			'hostname' => $hostname,
			'scriptid' => $scriptid,
			'script_name' => $script_name,
			'button_label' => $button_label,
			'button_color' => $button_color,
			'manualinput' => $manualinput,
			'manualinput_enabled' => $manualinput_enabled,
			'confirmation' => $confirmation,
			'show_details' => $show_details,
			'user' => [
				'debug_mode' => $this->getDebugMode()
			]
		]));
	}
}
