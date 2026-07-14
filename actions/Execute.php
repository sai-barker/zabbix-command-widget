<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Actions;

use API;
use CController;
use CControllerResponseData;
use CRoleHelper;

class Execute extends CController {

	protected function init(): void {
		$this->disableCsrfValidation();
	}

	protected function checkInput(): bool {
		$valid = $this->validateInput([
			'hostid' => 'required|db hosts.hostid',
			'scriptid' => 'required|db scripts.scriptid',
			'manualinput' => 'string'
		]);

		if (!$valid) {
			$this->sendJson([
				'success' => false,
				'error' => _('Invalid host, script or manual input.'),
				'messages' => array_column(
					get_and_clear_messages(),
					'message'
				)
			]);
		}

		return $valid;
	}

	protected function checkPermissions(): bool {
		if (!$this->checkAccess(CRoleHelper::ACTIONS_EXECUTE_SCRIPTS)) {
			return false;
		}

		return (bool) API::Host()->get([
			'output' => [],
			'hostids' => [$this->getInput('hostid')]
		]);
	}

	protected function doAction(): void {
		$parameters = [
			'hostid' => $this->getInput('hostid'),
			'scriptid' => $this->getInput('scriptid')
		];

		if ($this->hasInput('manualinput')) {
			$parameters['manualinput'] = $this->getInput('manualinput');
		}

		$result = API::Script()->execute($parameters);

		if (!$result) {
			$messages = array_column(
				get_and_clear_messages(),
				'message'
			);

			$this->sendJson([
				'success' => false,
				'error' => $messages
					? implode(' ', $messages)
					: _('Script execution failed.')
			]);

			return;
		}

		$this->sendJson([
			'success' => true,
			'output' => $result['value'] ?? ''
		]);
	}

	private function sendJson(array $data): void {
		$this->setResponse(
			(new CControllerResponseData([
				'main_block' => json_encode(
					$data,
					JSON_UNESCAPED_SLASHES
					| JSON_UNESCAPED_UNICODE
				)
			]))->disableView()
		);
	}
}