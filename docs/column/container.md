=info=[Info] Can add ^Mesour\UI\Button^, ^Mesour\UI\DropDown^ or ^Mesour\UI\Control^ too :-)

=info=[Info] Container column have default disabled ordering and filtering

## Implements
- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)
- ^Mesour\DataGrid\Column\IOrdering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iordering)
- ^Mesour\DataGrid\Column\IFiltering^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-ifiltering)
- ^Mesour\DataGrid\Column\IExportable^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-iexportable)
- ^Mesour\DataGrid\Column\IContainer^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icontainer)

## Methods for ^Mesour\DataGrid\Column\Container^

| Method         | Default  | Possible values                                | Returns                            | Required | Description                                                                                |
|----------------|:--------:|------------------------------------------------|------------------------------------|----------|--------------------------------------------------------------------------------------------|
| `addText`      |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Text^      |    no    | Add column text                                                                            |
| `addDate`      |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Date^      |    no    | Add column date                                                                            |
| `addContainer` |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Container^ |    no    | Add column container                                                                       |
| `addImage`     |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Image^     |    no    | Add column image                                                                           |
| `addStatus`    |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Status^    |    no    | Add column status                                                                          |
| `addTemplate`  |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\DataGrid\Column\Template^  |    no    | Add column template                                                                        |
| `addButton`    |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\UI\Button^                 |    no    | Add button                                                                                 |
| `addDropDown`  |  *none*  | string `$name`, `$header` = NULL               | ^Mesour\UI\DropDown^               |    no    | Add dropdown                                                                               |
| `addComponent` |  *none*  | ^Mesour\UI\Control^ `$control`, `$name` = NULL | ^Mesour\UI\Control^                |    no    | Add control                                                                                |
| `setCallback`  |  *none*  | callable `$callback`                           | ^Mesour\DataGrid\Column\Container^ |    no    | Fired before component is rendered. Container will add to column output from this callback |

### Callback parameters for method `setCallback`

| Parameter  | Type                                            | Description                                  |
|------------|:-----------------------------------------------:|----------------------------------------------|
| `$column`  | ^Mesour\DataGrid\Column\Container^              | Container column instance                    |
| `$rowData` | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row                         |
| `$span`    | ^Mesour\Components\Html^                        | Span instance, can change it                 |
| `$control` | ^Mesour\Comopnents\Control^                     | Current control / column / button / dropdown |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('containerDataGrid', $mesourApp);

$grid->setSource($source);

$grid->enablePager(5);

//! here create container column
$container = $grid->addContainer('surname', 'Name')
    ->setOrdering(TRUE);

//! here add columns to container
$container->addText('surname', 'Surname');

$container->addText('name', 'Name');

$grid->addText('email', 'E-mail');

//! here create container column for actions
$container = $grid->addContainer('actions', 'Actions')
    ->setOrdering(TRUE);

//! here add buttons to container
$container->addButton('edit')
    ->setIcon('pencil')
    ->setType('primary')
    ->setAttribute('href', '#');

$container->addButton('delete')
    ->setIcon('trash')
    ->setType('danger')
    ->setConfirm('Really delete item?')
    ->setAttribute('href', '#');

$grid->render();
```