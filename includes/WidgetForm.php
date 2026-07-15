<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Includes;

use API;

use Zabbix\Widgets\{
	CWidgetField,
	CWidgetForm
};

use Zabbix\Widgets\Fields\{
	CWidgetFieldCheckBox,
	CWidgetFieldColor,
	CWidgetFieldMultiSelectHost,
	CWidgetFieldSelect,
	CWidgetFieldTextArea,
	CWidgetFieldTextBox
};

class WidgetForm extends CWidgetForm {
	private const COMMAND_COUNT = 6;

	public function validate(bool $strict = false): array {
		$errors = parent::validate($strict);

		if ($errors) {
			return $errors;
		}

		$scriptids = [];

		for ($index = 1; $index <= self::COMMAND_COUNT; $index++) {
			$scriptid = $this->getFieldValue($this->getFieldName($index, 'scriptid'));

			if ($scriptid) {
				$scriptids[] = $scriptid;
			}
		}

		$scripts = $scriptids
			? API::Script()->get([
				'output' => [
					'manualinput',
					'manualinput_validator_type',
					'manualinput_validator'
				],
				'scriptids' => $scriptids,
				'preservekeys' => true
			])
			: [];

		for ($index = 1; $index <= self::COMMAND_COUNT; $index++) {
			$scriptid = $this->getFieldValue($this->getFieldName($index, 'scriptid'));

			if (!$scriptid) {
				continue;
			}

			if (!array_key_exists($scriptid, $scripts)) {
				$errors[] = _s(
					'Button %1$d: the selected script is unavailable or you do not have permission to use it.',
					$index
				);
				continue;
			}

			$script = $scripts[$scriptid];

			if ((int) $script['manualinput'] !== ZBX_SCRIPT_MANUALINPUT_ENABLED) {
				continue;
			}

			$manualinput = (string) $this->getFieldValue($this->getFieldName($index, 'manualinput'));
			$validator = $script['manualinput_validator'];

			if ((int) $script['manualinput_validator_type'] === ZBX_SCRIPT_MANUALINPUT_TYPE_LIST) {
				$allowed_values = array_map('trim', explode(',', $validator));

				if (!in_array($manualinput, $allowed_values, true)) {
					$errors[] = _s(
						'Button %1$d manual input must be one of: %2$s.',
						$index,
						implode(', ', $allowed_values)
					);
				}

				continue;
			}

			$regex_validator = new \CRegexValidator([
				'messageInvalid' => _('The input validation rule must be a string.'),
				'messageRegex' => _('Incorrect regular expression "%1$s": "%2$s"')
			]);

			if (!$regex_validator->validate($validator)) {
				$errors[] = _s('Button %1$d: %2$s', $index, $regex_validator->getError());
				continue;
			}

			$regular_expression = '/'.str_replace('/', '\\/', $validator).'/';

			if (!preg_match($regular_expression, trim($manualinput))) {
				$errors[] = _s(
					'Button %1$d manual input does not match the script validation rule: %2$s',
					$index,
					$validator
				);
			}
		}

		return $errors;
	}

	public function addFields(): self {
		$scripts = API::Script()->get([
			'output' => ['scriptid', 'name'],
			'filter' => ['scope' => 2],
			'sortfield' => 'name'
		]);

		$script_options = [0 => _('Select a script')];

		foreach ($scripts as $script) {
			$script_options[(int) $script['scriptid']] = $script['name'];
		}

		$this->addField(
			(new CWidgetFieldMultiSelectHost('hostid', _('Host')))
				->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK)
				->setMultiple(false)
		);

		for ($index = 1; $index <= self::COMMAND_COUNT; $index++) {
			$script_field = new CWidgetFieldSelect(
				$this->getFieldName($index, 'scriptid'),
				_('Script'),
				$script_options
			);

			$script_field->setDefault(0);

			if ($index === 1) {
				$script_field->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK);
			}

			$this
				->addField($script_field)
				->addField(
					(new CWidgetFieldTextBox($this->getFieldName($index, 'label'), _('Button label')))
						->setDefault($index === 1 ? _('Execute') : '')
				)
				->addField(
					(new CWidgetFieldColor($this->getFieldName($index, 'color'), _('Button color')))
						->setDefault('0275B8')
				)
				->addField(
					(new CWidgetFieldTextArea($this->getFieldName($index, 'manualinput'), _('Manual input')))
						->setDefault('')
				);
		}

		return $this->addField(
			(new CWidgetFieldCheckBox('show_details', _('Show host and script details')))->setDefault(0)
		);
	}

	private function getFieldName(int $index, string $field): string {
		if ($index === 1) {
			return 'command_'.$field;
		}

		return 'command_'.$index.'_'.$field;
	}
}
