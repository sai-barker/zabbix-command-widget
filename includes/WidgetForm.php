<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Includes;

use API;

use Zabbix\Widgets\{
	CWidgetField,
	CWidgetForm
};

use Zabbix\Widgets\Fields\{
	CWidgetFieldCheckBox,
	CWidgetFieldMultiSelectHost,
	CWidgetFieldSelect,
	CWidgetFieldTextArea,
	CWidgetFieldTextBox
};

class WidgetForm extends CWidgetForm {

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
				(new CWidgetFieldTextArea('command_manualinput', _('Manual input')))
					->setDefault('')
			)
			->addField(
				(new CWidgetFieldCheckBox('show_details', _('Show host and script details')))
					->setDefault(0)
			);
	}
}
