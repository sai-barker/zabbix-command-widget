<?php declare(strict_types = 0);
/*
** Zabbix
** Copyright (C) 2001-2023 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


namespace Modules\LessonGaugeChart\Actions;

use API,
	CControllerDashboardWidgetView,
	CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {

	protected function doAction(): void {
		$db_items = API::Item()->get([
			'output' => ['itemid', 'value_type', 'name', 'units'],
			'itemids' => $this->fields_values['itemid'],
			'webitems' => true,
			'filter' => [
				'value_type' => [ITEM_VALUE_TYPE_UINT64, ITEM_VALUE_TYPE_FLOAT]
			]
		]);

		$history_value = null;

		if ($db_items) {
			$item = $db_items[0];

			$history = API::History()->get([
				'output' => API_OUTPUT_EXTEND,
				'itemids' => $item['itemid'],
				'history' => $item['value_type'],
				'sortfield' => 'clock',
				'sortorder' => ZBX_SORT_DOWN,
				'limit' => 1
			]);

			if ($history) {
				$history_value = convertUnitsRaw([
					'value' => $history[0]['value'],
					'units' => $item['units']
				]);
			}
		}

		$this->setResponse(new CControllerResponseData([
			'name' => $this->getInput('name', $this->widget->getName()),
			'history' => $history_value,
			'fields_values' => $this->fields_values,
			'user' => [
				'debug_mode' => $this->getDebugMode()
			]
		]));
	}
}
