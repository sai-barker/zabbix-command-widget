<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

(new CWidgetView($data))
    ->addItem(
        (new CButton('execute', 'Execute'))
            ->addClass('js-command-widget-execute')
    )
    ->show();