<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Includes;

use Zabbix\Widgets\CWidgetForm;

class WidgetForm extends CWidgetForm {

    public function addFields(): self {
        return $this;
    }
}