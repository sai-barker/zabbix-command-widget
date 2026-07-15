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

	public function validate(bool $strict = false): array {
		$errors = parent::validate($strict);

		if ($errors) {
			return $errors;
		}

		$scriptid = $this->getFieldValue('command_scriptid');
		$manualinput = (string) $this->getFieldValue('command_manualinput');

		$scripts = API::Script()->get([
			'output' => [
				'manualinput',
				'manualinput_validator_type',
				'manualinput_validator'
			],
			'scriptids' => [$scriptid]
		]);

		if (!$scripts) {
			return [_('The selected script is unavailable or you do not have permission to use it.')];
		}

		$script = $scripts[0];

		if ((int) $script['manualinput'] !== ZBX_SCRIPT_MANUALINPUT_ENABLED) {
			return $errors;
		}

		$validator = $script['manualinput_validator'];

		if ((int) $script['manualinput_validator_type'] === ZBX_SCRIPT_MANUALINPUT_TYPE_LIST) {
			$allowed_values = array_map('trim', explode(',', $validator));

			if (!in_array($manualinput, $allowed_values, true)) {
				$errors[] = _s(
					'Manual input must be one of: %1$s.',
					implode(', ', $allowed_values)
				);
			}

			return $errors;
		}

		$regex_validator = new \CRegexValidator([
			'messageInvalid' => _('The input validation rule must be a string.'),
			'messageRegex' => _('Incorrect regular expression "%1$s": "%2$s"')
		]);

		if (!$regex_validator->validate($validator)) {
			$errors[] = $regex_validator->getError();

			return $errors;
		}

		$regular_expression = '/'.str_replace('/', '\\/', $validator).'/';

		if (!preg_match($regular_expression, trim($manualinput))) {
			$errors[] = _s(
				'Manual input does not match the script validation rule: %1$s',
				$validator
			);
		}

		return $errors;
	}

	public function addFields(): self {
		$scripts = API::Script()->get([
			'output' => ['scriptid', 'name'],
			'filter' => [
				'scope' => 2
			],
			'sortfield' => 'name'
		]);

		$script_options = [
			0 => _('Select a script')
		];

		foreach ($scripts as $script) {
			$script_options[(int) $script['scriptid']] = $script['name'];
		}

		return $this
			->addField(
				(new CWidgetFieldMultiSelectHost('hostid', _('Host')))
					->setFlags(
						CWidgetField::FLAG_NOT_EMPTY
						| CWidgetField::FLAG_LABEL_ASTERISK
					)
					->setMultiple(false)
			)
			->addField(
				(new CWidgetFieldSelect(
					'command_scriptid',
					_('Script'),
					$script_options
				))
					->setDefault(0)
					->setFlags(
						CWidgetField::FLAG_NOT_EMPTY
						| CWidgetField::FLAG_LABEL_ASTERISK
					)
			)
			->addField(
				(new CWidgetFieldTextBox('command_label', _('Button label')))
					->setDefault(_('Execute'))
			)
			->addField(
				(new CWidgetFieldColor('command_color', _('Button color')))
					->setDefault('0275B8')
			)
			->addField(
				(new CWidgetFieldTextArea('command_manualinput', _('Manual input')))
					->setDefault('')
			)
			->addField(
				(new CWidgetFieldCheckBox('show_details', _('Show host and script details')))
					->setDefault(0)
			);
	}
}
