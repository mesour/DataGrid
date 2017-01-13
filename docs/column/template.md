## Implements
- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)
- ^Mesour\DataGrid\Column\IOrdering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iordering)
- ^Mesour\DataGrid\Column\IFiltering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-ifiltering)
- ^Mesour\DataGrid\Column\IExportable^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iexportable)

## Methods for ^Mesour\DataGrid\Column\Template^

| Method             | Default | Possible values        | Returns                           | Required | Description                                    |
|--------------------|:-------:|------------------------|-----------------------------------|----------|------------------------------------------------|
| `setTempDirectory` |   `0`   | int `$decimals`        | ^Mesour\DataGrid\Column\Template^ | **yes**  | Path to writable temp dir                      |
| `setTemplateFile`  |   `.`   | string `$decimalPoint` | ^Mesour\DataGrid\Column\Template^ | **yes**  | Path to template file                          |
| `setBlock`         |  *none* | string `$unit`         | ^Mesour\DataGrid\Column\Template^ | no       | Specify block in your template                 |
| `setCallback`      |  *none* | callable `$callback`   | ^Mesour\DataGrid\Column\Template^ | no       | Can add variables to template in this callback |

### Callback parameters for method `setCallback`

| Parameter       |                       Type                      | Description              |
|-----------------|:-----------------------------------------------:|--------------------------|
| `$column`       |        ^Mesour\DataGrid\Column\Template^        | Template column instance |
| `$rowData`      | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row     |
| `$templateFile` |          ^Mesour\DataGrid\TemplateFile^         | Template file instance   |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('numberDataGrid', $mesourApp);

$grid->setSource($source);

//! here add column template
$grid->addTemplate('name', 'Name')
    ->setTempDirectory( __DIR__ . '/../../../temp/cache')
    ->setTemplateFile(__DIR__ . '/../templates/test.latte')
    ->setBlock('test2')
    ->setCallback(function (\Mesour\DataGrid\Column\Template $column, $data, \Mesour\DataGrid\TemplateFile $templateFile) {
        $templateFile->name = $data['name'];
    });

$grid->addText('surname', 'Surname');

$grid->addText('email', 'E-mail');

$grid->render();
```