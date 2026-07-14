<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$items = [
    new CDiv('Hello World'),
    new CTag('hr'),
    new CDiv('Host ID: '.$data['hostid']),
    new CDiv('Host Name: '.$data['hostname']),
    new CTag('hr'),
    new CTag('h4', true, 'Available Scripts')
];

foreach ($data['scripts'] as $script) {
    $items[] = new CDiv(
        sprintf(
            '%s - %s (Scope:%s Type:%s)',
            $script['scriptid'],
            $script['name'],
            $script['scope'],
            $script['type']
        )
    );
}

(new CWidgetView($data))
    ->addItem($items)
    ->show();