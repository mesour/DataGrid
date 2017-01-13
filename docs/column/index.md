## Interfaces

### Interface ^Mesour\Table\Render\IColumn^

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Image^ - {Column:image}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Status^ - {Column:status}
- ^Mesour\DataGrid\Column\Template^ - {Column:template}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IColumn^

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Image^ - {Column:image}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Status^ - {Column:status}
- ^Mesour\DataGrid\Column\Template^ - {Column:template}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IOrdering^

=primary=[Used by] {Basic:ordering}

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Status^ - {Column:status}
- ^Mesour\DataGrid\Column\Template^ - {Column:template}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IFiltering^

=primary=[Used by] {Filter:default}

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Status^ - {Column:status}
- ^Mesour\DataGrid\Column\Template^ - {Column:template}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IInlineEdit^

=primary=[Used by] {Editable:default}

#### Implemented by

- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IExportable^

=primary=[Used by] {Export:default}

=info=[Info] This interface only says: "This column can be exported". Method `getBodyContent` will be used by ^Mesour\DataGrid\Extensions\Export\ExportExtension^.

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
- ^Mesour\DataGrid\Column\Date^ - {Column:date}
- ^Mesour\DataGrid\Column\Number^ - {Column:number}
- ^Mesour\DataGrid\Column\Text^ - {Column:text}

### Interface ^Mesour\DataGrid\Column\IContainer^

=info=[Info] This interface only says: "This column is container, column name may not exist in the data". Can use every string as column name :-)

#### Implemented by

- ^Mesour\DataGrid\Column\Container^ - {Column:container}
