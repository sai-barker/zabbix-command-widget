<?php declare(strict_types =0);

namespace Modules\ZabbixCommandWidget\Includes;

use Zabbix\Widgets\{
    CWidgetField,
    CWidgetForm
};

use Zabbix\Widgets\Fields\CWidgetFieldMultiSelectHost;

class WidgetForm extends CWidgetForm {

    public function addFields(): self {

        return $this
            ->addField(
                (new CWidgetFieldMultiSelectHost('hostid', _('Host')))
                    ->setFlags(
                        CWidgetField::FLAG_NOT_EMPTY |
                        CWidgetField::FLAG_LABEL_ASTERISK
                    )
                    ->setMultiple(false)
            );
    }
}