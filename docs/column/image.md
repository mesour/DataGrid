## Implements
- ^Mesour\Table\Render\IColumn^ - [Implemented by](/version3/column/#interface-mesour-table-render-icolumn)
- ^Mesour\DataGrid\Column\IColumn^ - [Implemented by](/version3/column/#interface-mesour-datagrid-column-icolumn)

## Methods for ^Mesour\DataGrid\Column\Image^

| Method           |                              Default                             | Possible values                                                       | Returns                        | Required | Description                                                                                                                      |
|------------------|:----------------------------------------------------------------:|-----------------------------------------------------------------------|--------------------------------|----------|----------------------------------------------------------------------------------------------------------------------------------|
| `setMaxWidth`    |                              *none*                              | string Number or "80px", "1em"...                                     | ^Mesour\DataGrid\Column\Image^ | no       | Set max image width                                                                                                              |
| `setMaxHeight`   |                              *none*                              | string Number or "80px", "1em"...                                     | ^Mesour\DataGrid\Column\Image^ | no       | Set max image height                                                                                                             |
| `setPreviewPath` | *none*, `$_SERVER['DOCUMENT_ROOT']`, `$_SERVER['DOCUMENT_ROOT']` | `$previewWebPath`, `$previewRootPath` = NULL, `$imageRootPath` = NULL | ^Mesour\DataGrid\Column\Image^ | no       | Set preview path for resizing images                                                                                             |
| `setCallback`    |                              *none*                              | callable `$callback`                                                  | ^Mesour\DataGrid\Column\Image^ | no       | If you use callback, column shows output of this callback as image src attribute *(only if not using temp dir and image resize)* |

### Callback parameters for method `setCallback`

| Parameter  |                       Type                      | Description           |
|------------|:-----------------------------------------------:|-----------------------|
| `$column`  |          ^Mesour\DataGrid\Column\Image^         | Image column instance |
| `$rowData` | ^Mesour\Sources\ArrayHash^ / Entity / ActiveRow | Data for current row  |

## Events

=info=[Info] See [`onRender`on events](/version3/rendering/events/#event-onrender-on-mesour-datagrid-column-icolumn) page

## Usage

```php
$mesourApp = //instance Mesour\Components\Application\IApplication
$source = //some <a href="http://components.mesour.com/version3/component/sources/" target="_blank">data source</a> or two-dimensional array
$source->setPrimaryKey('user_id');

$grid = new Mesour\UI\DataGrid('numberDataGrid', $mesourApp);

$grid->setSource($source);

//! here add image column
$grid->addImage('avatar', 'Avatar')
    ->setPreviewPath(
        '/avatar/preview',
        __DIR__ . '/../../../htdocs',
        __DIR__ . '/../../../htdocs'
    )
    ->setMaxHeight(80) // translated as max-height: 80px;
    ->setMaxWidth(80); // can use 80, "80px", "1em"...

$grid->addText('name', 'Name');

$grid->addText('surname', 'Surname');

$grid->addText('email', 'E-mail');
```