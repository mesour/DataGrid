## Implements
- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)
- ^Mesour\DataGrid\Column\IOrdering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iordering)
- ^Mesour\DataGrid\Column\IFiltering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-ifiltering)
- ^Mesour\DataGrid\Column\IExportable^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iexportable)
- ^Mesour\DataGrid\Column\IInlineEdit^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iinlineedit)

## Methods for ^Mesour\DataGrid\Column\Number^

| Method                  | Default | Possible values        | Returns                         | Required | Description                                               |
|-------------------------|:-------:|------------------------|---------------------------------|----------|-----------------------------------------------------------|
| `setDecimals`           |   `0`   | int `$decimals`        | ^Mesour\DataGrid\Column\Number^ | no       | Set the number of decimal places                          |
| `setDecimalPoint`       |   `.`   | string `$decimalPoint` | ^Mesour\DataGrid\Column\Number^ | no       | Set decimal point                                         |
| `setThousandsSeparator` |   `,`   | string `$separator`    | ^Mesour\DataGrid\Column\Number^ | no       | Set thousands separator                                   |
| `setUnit`               |  *none* | string `$unit`         | ^Mesour\DataGrid\Column\Number^ | no       | Set unit for this number                                  |
| `setCallback`           |  *none* | callable `$callback`   | ^Mesour\DataGrid\Column\Number^ | no       | If you use callback, column shows output of this callback |

### Callback parameters for method `setCallback`

| Parameter    |                       Type                      | Description            |
|--------------|:-----------------------------------------------:|------------------------|
| `$column`    |         ^Mesour\DataGrid\Column\Number^         | Number column instance |
| `$rowData`   | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row   |
| `$formatted` |                      string                     | Formatted number       |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('numberDataGrid', $mesourApp);

$grid->setSource($source);

$grid->addText('name', 'Name');

$grid->addText('surname', 'Surname');

//! here add status column
$grid->addNumber('amount', 'Amount')
    ->setUnit('USD')
    ->setThousandsSeparator(',')
    ->setDecimals(2)
    ->setDecimalPoint('.');

$grid->addText('email', 'E-mail');

$grid->render();
```