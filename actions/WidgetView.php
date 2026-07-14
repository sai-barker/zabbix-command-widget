<?php declare(strict_types = 0);

namespace Modules\ZabbixCommandWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {

    protected function doAction(): void {

        $hostid = $this->fields_values['hostid'][0] ?? null;

        $hostname = '(None)';

        if ($hostid !== null) {
            $hosts = API::Host()->get([
                'output' => ['host', 'name'],
                'hostids' => [$hostid]
            ]);

            if ($hosts) {
                $hostname = $hosts[0]['name'];
            }
        }

        $scripts = API::Script()->get([
            'output' => [
                'scriptid',
                'name',
                'scope',
                'type'
            ],
            'sortfield' => 'name'
        ]);

        $this->setResponse(new CControllerResponseData([
            'name' => $this->getInput('name', $this->widget->getName()),
            'hostid' => $hostid,
            'hostname' => $hostname,
            'scripts' => $scripts,
            'user' => [
                'debug_mode' => $this->getDebugMode()
            ]
        ]));
    }
}