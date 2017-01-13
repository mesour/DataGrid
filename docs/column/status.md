## Implements
- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)
- ^Mesour\DataGrid\Column\IOrdering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iordering)
- ^Mesour\DataGrid\Column\IFiltering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-ifiltering)

## Methods for ^Mesour\DataGrid\Column\Status^

| Method         | Default | Possible values                                                          | Returns                                        | Required | Description                           |
|----------------|:-------:|--------------------------------------------------------------------------|------------------------------------------------|----------|---------------------------------------|
| `addButton`    |  *none* | *none*                                                                   | ^Mesour\DataGrid\Column\Status\StatusButton^   | no       | Add status button to column           |
| `addDropDown`  |  *none* | *none*                                                                   | ^Mesour\DataGrid\Column\Status\StatusDropDown^ | no       | Add status dropdown to column         |
| `addComponent` |  *none* | ^Mesour\DataGrid\Column\Status\IStatusItem^ `$component`, `$name` = NULL | ^Mesour\DataGrid\Column\Status\IStatusItem^    | no       | Add custom status component to column |
| `setCallback`  |  *none* | callable `$callback`                                                     | ^Mesour\DataGrid\Column\Status^                | no       | Fired before button is created        |

### Callback parameters for method `setCallback`

| Parameter    |                       Type                      | Description                        |
|--------------|:-----------------------------------------------:|------------------------------------|
| `$column`    |         ^Mesour\DataGrid\Column\Status^         | Status column instance             |
| `$component` |   ^Mesour\DataGrid\Column\Status\IStatusItem^   | Status component instance          |
| `$rowData`   | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row               |
| `$isActive`  |                       bool                      | TRUE if is status component active |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

### Interface ^Mesour\DataGrid\Column\Status\IStatusItem^

| Method             |              Parameters             | Description                    | Returns                                             |
|--------------------|:-----------------------------------:|--------------------------------|-----------------------------------------------------|
| `isActive`         | string `$columnName`, array `$data` | Check if item is active        | bool                                                |
| `setStatus`        |        string / int `$status`       | Set current status             | ^Mesour\DataGrid\Column\Status\IStatusItem^         |
| `getStatusOptions` |               **none**              | Returns current status options | array / null `[$this->status => $this->statusName]` |
| `getStatus`        |               **none**              | Returns current status         | string / int                                        |

## [Row selection](/version3/basic/selection/) integration

=info=[Info] Can set status name for ^Mesour\DataGrid\Column\Status\IStatusItem^

```php
$status = $grid->addStatus('action', 'S');

$statusButton = $status->addButton('active');

//! second parameter is status name
$statusButton->setStatus(1, 'All active');
```

=info=[Info] See [Demo](#demo) and use main checkbox dropdown

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('statusDataGrid', $mesourApp);

$grid->setSource($source);

$grid->enableRowSelection();

//! here create status column
$statusColumn = $grid->addStatus('action', 'S');

$statusColumn->addButton('active')
    ->setStatus(1, 'Active', 'All active')
    ->setIcon('check-circle')
    ->setType('success')
    ->setAttribute('href', '#');

$statusColumn->addButton('inactive')
    ->setStatus(0, 'Inactive', 'All inactive')
    ->setIcon('times-circle')
    ->setType('danger')
    ->setAttribute('href', '#');

$grid->addText('name', 'Name');

$grid->addText('surname', 'Surname');

$grid->addText('email', 'E-mail');
```