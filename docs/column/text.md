## Implements

- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)
- ^Mesour\DataGrid\Column\IOrdering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iordering)
- ^Mesour\DataGrid\Column\IFiltering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-ifiltering)
- ^Mesour\DataGrid\Column\IExportable^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iexportable)
- ^Mesour\DataGrid\Column\IInlineEdit^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iinlineedit)

## Methods for ^Mesour\DataGrid\Column\Text^

| Method        | Default | Possible values      | Returns                       | Required | Description                                               |
|---------------|:-------:|----------------------|-------------------------------|----------|-----------------------------------------------------------|
| `setCallback` |  *none* | callable `$callback` | ^Mesour\DataGrid\Column\Text^ | no       | If you use callback, column shows output of this callback |

### Callback parameters for method `setCallback`

| Parameter  |                       Type                      | Description          |
|------------|:-----------------------------------------------:|----------------------|
| `$column`  |          ^Mesour\DataGrid\Column\Text^          | Text column instance |
| `$rowData` | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('numberDataGrid', $mesourApp);

$grid->setSource($source);

//! here add some text columns
$grid->addText('surname', 'Name with surname')
    ->setCallback(function (Mesour\DataGrid\Column\Text $column, $rowData) {
        return $rowData['name'] . ' ' . $rowData['surname'];
    });

$grid->addText('email', 'E-mail')
    ->setCallback(function (Mesour\DataGrid\Column\Text $column, $rowData) {
        return Mesour\Components\Utils\Html::el('a', [
            'href' => 'mailto:' . $rowData['email']
        ])->add($rowData['email']);
    });

$grid->render();
```