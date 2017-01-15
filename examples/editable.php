<?php

define('SRC_DIR', __DIR__ . '/../src/');

require_once __DIR__ . '/../vendor/autoload.php';

@mkdir(__DIR__ . '/log');
@mkdir(__DIR__ . '/temp');

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT, __DIR__ . '/log');
\Tracy\Debugger::$strictMode = true;

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__ . '/../src');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__ . '/temp'));
$loader->register();

?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
	  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<link rel="stylesheet" href="../node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

<link rel="stylesheet" href="../node_modules/mesour-datagrid/dist/css/mesour.datagrid.min.css">

<style>
    .input-group-btn:last-child > .btn[data-simple-filter] {
        padding: 9px;
    }
</style>

<hr>

<div class="row col-lg-12" style="padding-left: 50px;">
	<h2>Basic functionality</h2>

	<hr>

	<?php

	$time_start = microtime(true);

	$sourceFile = 'nette_source';
	$primaryKey = 'userId';

	$application = new \Mesour\UI\Application('mesourapp');

	$application->getConfiguration()
        ->setTempDir(__DIR__ . '/temp');

	$application->setRequest($_REQUEST);

	$application->getUser()->setRoles('registered');

	$auth = $application->getAuthorizator();

	$auth->addRole('guest');
	$auth->addRole('registered', 'guest');

	$auth->addResource('menu');

	$auth->allow('guest', 'menu', ['first', 'second']);
	$auth->allow('registered', 'menu');
	$auth->deny('registered', 'menu', 'second');

	$grid = new \Mesour\UI\DataGrid('basicDataGrid', $application);

	$wrapper = $grid->getWrapperPrototype();

	$wrapper->class('my-class');

	// TRUE = append
	$wrapper->class('my-next-class', true);

	/** @var \Mesour\DataGrid\Sources\IGridSource $source */
	$source = require_once __DIR__ . '/sources/' . $sourceFile . '.php';

	$dataStructure = $source->getDataStructure();

	$dataStructure->renameColumn('user_addresses', 'addresses');
	$dataStructure->renameColumn('groups', 'group');
	$dataStructure->renameColumn('wallets', 'wallet');

	/** @var \Mesour\Sources\Structures\Columns\ManyToManyColumnStructure $companiesColumn */
	$companiesColumn = $dataStructure->getColumn('companies');
	$companiesColumn->setPattern('{name}');

	/** @var \Mesour\Sources\Structures\Columns\OneToManyColumnStructure $addressesColumn */
	$addressesColumn = $dataStructure->getColumn('addresses');
	$addressesColumn->setPattern('{street}, {zip} {city}, {country}');

	/** @var \Mesour\Sources\Structures\Columns\ManyToOneColumnStructure $groupColumn */
	$groupColumn = $dataStructure->getColumn('group');
	$groupColumn->setPattern('{name} ({type})');

	/** @var \Mesour\Sources\Structures\Columns\OneToOneColumnStructure $walletColumn */
	$walletColumn = $dataStructure->getColumn('wallet');
	$walletColumn->setPattern('{amount}');

	$grid->setSource($source);

	$pager = $grid->enablePager(8);

	//$filter = $grid->enableFilter();
	$filter = $grid->enableSimpleFilter();

	$selection = $grid->enableRowSelection();

	$selection = $selection->getLinks();

	$selection->addHeader('Active');

	$selection->addLink('Active')// add selection link
	->onCall[] = function () {
		dump('ActivateSelected', func_get_args());
	};

	$selection->addLink('Unactive')
		->setAjax(false)// disable AJAX
		->onCall[] = function () {
		dump('InactivateSelected', func_get_args());
	};

	$selection->addDivider();

	$selection->addLink('Delete')
		->setConfirm('Really delete all selected users?')// set confirm text
		->onCall[] = function () {
		dump('DeleteSelected', func_get_args());
	};

	// EDITABLE

	$editable = $grid->enableEditable();

	$editableStructure = $editable->getDataStructure();

	$editableStructure->addOneToOne('wallet', 'Wallet')
		->enableCreateNewRow();

	$editableStructure->addManyToOne('group', 'Groups')
		->enableEditCurrentRow()
		->enableCreateNewRow()
		->setNullable();

	$editableStructure->addOneToMany('addresses', 'Addresses')
		->enableCreateNewRow()
        ->enableRemoveRow();

	$editableStructure->addManyToMany('companies', 'Companies')
		->enableAttachRow()
		->enableCreateNewRow()
		->enableRemoveRow();

	$companyStructure = $editableStructure->getOrCreateElement('companies', 'id');
	$companyStructure->addText('name', 'Name');
	$companyStructure->addNumber('reg_num', 'Reg. number');
	$companyStructure->addBool('verified', 'Verified');

	$walletStructure = $editableStructure->getOrCreateElement('wallets', 'id');
	$walletStructure->addNumber('amount', 'Amount')
		->setDecimals(2)
		->setThousandSeparator('.')
		->setDecimalPoint(',');
	$walletStructure->addEnum('currency', 'Currency')
		->addValue('CZK', 'CZK')
		->addValue('EUR', 'EUR');

	$groupsStructure = $editableStructure->getOrCreateElement('groups', 'id');
	$groupsStructure->addText('name', 'Name');
	$groupsStructure->addEnum('type', 'Type')
		->setNullable()
		->addValue('first', 'First')
		->addValue('second', 'Second');
	$groupsStructure->addDate('date', 'Date');
	$groupsStructure->addNumber('members', 'Members');

	// / EDITABLE

    $grid->enableSortable('sort');

	$status = $grid->addStatus('action', 'S')
		->setPermission('menu', 'second');

	$status->addButton('active')
		->setStatus(1, 'Active', 'All active')
		->setIcon('check-circle-o')
		->setType('success')
		->setAttribute('href', '#');

	$status->addButton('inactive')
		->setStatus(0, 'Inactive', 'All inactive')
		->setIcon('times-circle-o')
		->setType('danger')
		->setAttribute('href', '#');

	$grid->addText('name', 'Name');

	$grid->addText('email', 'E-mail');

	$grid->addText('role', 'Role');

	$grid->addDate('last_login', 'Last login')
		->setFormat('Y-m-d');

	$grid->addText('has_pro', 'Has pro')
		->setAttribute('title', 'Has pro')
		->setCallback(
			function (\Mesour\DataGrid\Column\Text $column, $data) {
				if($data['has_pro']) {
					return '<b style="color:green">Yes</b>';
				}
				return '<b style="color:red">No</b>';
			}
		);

	$grid->addText('group', 'Group')
		//->setFiltering(false)
		->setAttribute('title', 'Select group');

	$grid->addText('wallet', 'Wallet')
		//->setFiltering(false)
		->setAttribute('title', 'Wallet');

	$grid->addText('addresses', 'Addresses')
		//->setFiltering(false)
	;

	$grid->addText('companies', 'Companies')
		//->setFiltering(false)
	;

	$grid->addNumber('amount', 'Amount')
		->setUnit('CZK');

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	echo "<hr><b>Execution time (before render): " . number_format($time, 3, ',', ' ') . " seconds</b><hr>";

	echo $grid->render();

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	echo "<hr><b>Execution time (after render): " . number_format($time, 3, ',', ' ') . " seconds</b><hr>";

	?>
</div>

<hr>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<!-- Latest compiled and minified JavaScript -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
		integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
		crossorigin="anonymous"></script>

<script src="../node_modules/eonasdan-bootstrap-datetimepicker/node_modules/moment/min/moment.min.js"></script>
<script src="../node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<script src="../node_modules/mesour-datagrid/dist/js/mesour.datagrid.js"></script>
