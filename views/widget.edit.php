<?php declare(strict_types = 0);

/**
 * @var CView $this
 * @var array $data
 */

$form = new CWidgetFormView($data);

$form
    ->addField(
        new CWidgetFieldMultiSelectHostView($data['fields']['hostid'])
    )
    ->show();