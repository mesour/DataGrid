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

<link rel="stylesheet" href="../vendor/mesour/components/public/DateTimePicker/bootstrap-datetimepicker.min.css">

<link rel="stylesheet" href="../public/src/mesour.grid.css">
<link rel="stylesheet" href="../vendor/mesour/filter/public/mesour.filter.min.css">
<link rel="stylesheet" href="../vendor/mesour/selection/public/mesour.selection.css">


<hr>

<div class="container">
	<h2>Basic functionality</h2>

	<hr>

	<?php

	$time_start = microtime(true);

	$sourceFile = 'doctrine_source';
	$primaryKey = 'userId';

	$application = new \Mesour\UI\Application('mesourapp');

	$application->setRequest($_REQUEST);

	$application->setUserRole('registered');

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

	$grid->setPrimaryKey($primaryKey);

	/** @var \Mesour\DataGrid\Sources\IGridSource $source */
	$source = require_once __DIR__ . '/sources/' . $sourceFile . '.php';

	$source->setReference('groupName', \Mesour\Sources\Tests\Entity\Groups::class, 'name');

	$grid->setSource($source);

	$pager = $grid->enablePager(8);

	$filter = $grid->enableFilter();

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

	$sortable = $grid->enableSortable('sort');

	$editable = $grid->enableEditable();

	$export = $grid->enableExport(__DIR__ . '/temp');

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

	$grid->addText('groupName', 'Group');

	$grid->addNumber('amount', 'Amount')
		->setUnit('CZK');

	$container = $grid->addContainer('blablablablablabla', 'Actions');

	//$container->setDisabled();

	$button = $container->addButton('test_button');

	$button->setIcon('pencil')
		->setType('primary')
		->setAttribute('href', $button->link('http://mesour.com'))
		->setAttribute('target', '_blank');

	$dropDown = $container->addDropDown('test_drop_down')
		->setPullRight()
		->setAttribute('class', 'dropdown');

	$dropDown->addHeader('Test header');

	$first = $dropDown->addButton();

	$first->setText('First button')
		->setAttribute('href', $dropDown->link('/first/'));

	$dropDown->addDivider();

	$dropDown->addHeader('Test header 2');

	$dropDown->addButton()
		->setText('Second button')
		->setConfirm('Test confirm :-)')
		->setAttribute('href', $dropDown->link('/second/'));

	$dropDown->addButton()
		->setText('Third button')
		->setAttribute('href', $dropDown->link('/third/'));

	$mainButton = $dropDown->getMainButton();

	$mainButton->setText('Actions')
		->setType('danger');

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
<script src="../public/jquery.ui.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
		integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
		crossorigin="anonymous"></script>

<script src="../vendor/mesour/components/public/DateTimePicker/moment.min.js"></script>
<script src="../vendor/mesour/components/public/DateTimePicker/bootstrap-datetimepicker.min.js"></script>

<script src="../vendor/mesour/components/public/mesour.components.min.js"></script>

<script src="../vendor/mesour/filter/public/src/mesour.filter.js"></script>
<script src="../vendor/mesour/filter/public/src/mesour.filter.Checkers.js"></script>
<script src="../vendor/mesour/filter/public/src/mesour.filter.CustomFilter.js"></script>
<script src="../vendor/mesour/filter/public/src/mesour.filter.Filter.js"></script>
<script src="../vendor/mesour/filter/public/src/mesour.filter.DropDown.js"></script>

<script src="../vendor/mesour/selection/public/mesour.selection.js"></script>
<script src="../vendor/mesour/pager/public/mesour.advancedPager.js"></script>
<script src="../public/src/mesour.grid.core.js"></script>
<script src="../public/src/mesour.grid.sortable.js"></script>
<script src="../public/src/mesour.grid.selection.js"></script>
<script src="../public/src/mesour.grid.editable.js"></script>