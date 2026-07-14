<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {

	protected function doAction(): void {
		$hostid = $this->fields_values['hostid'][0] ?? null;
		$scriptid = $this->fields_values['command_scriptid'] ?? null;

		$hostname = _('Unknown host');
		$script_name = _('No script selected');

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
				'output' => ['name'],
				'scriptids' => [$scriptid]
			]);

			if ($scripts) {
				$script_name = $scripts[0]['name'];
			}
		}

		$this->setResponse(new CControllerResponseData([
			'name' => $this->getInput('name', $this->widget->getName()),
			'hostid' => $hostid,
			'hostname' => $hostname,
			'scriptid' => $scriptid,
			'script_name' => $script_name,
			'user' => [
				'debug_mode' => $this->getDebugMode()
			]
		]));
	}
}