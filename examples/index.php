<?php

define('SRC_DIR', __DIR__ . '/../src/');

require_once __DIR__ . '/../vendor/autoload.php';

use Mesour\Sources\Tests\Entity\User;

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

<hr>

<div class="container">
	<h2>Basic functionality</h2>

	<hr>

	<?php

	$time_start = microtime(true);

	$sourceFile = 'doctrine_source';
	$relatedTable = \Mesour\Sources\Tests\Entity\Group::class;
	$lastLogin = 'last_login';

	$application = new \Mesour\UI\Application('mesourapp');

	function getSubGrid()
	{
	    global $application;

		$_sub_grid = new \Mesour\UI\DataGrid('subGrid', $application);

		$_sub_grid->enablePager(5);

		$_sub_grid->onEditCell[] = function () {
			dump(func_get_args());
		};

		$_sub_grid->enableSortable('sort');

		$_sub_grid->onSort[] = function () {
			dump(func_get_args());
		};

		$_sub_grid->addText('name');

		$_sub_grid->addText('surname');

		$_sub_grid->enableFilter();

		$selection = $_sub_grid->enableRowSelection()
			->getLinks();

		$selection->addLink('Active')
			->onCall[] = function () {
			dump(func_get_args());
		};

		$selection->addLink('Unactive')
			->setAjax(false)
			->onCall[] = function () {
			dump(func_get_args());
		};

		$selection->addLink('Delete')
			->setConfirm('Really delete all selected users?')
			->onCall[] = function () {
			dump(func_get_args());
		};

		return $_sub_grid;
	}

	function createTestButton(\Mesour\Components\Control\IControl $parent, $name)
	{
		$button = new \Mesour\UI\Button($name, $parent);

		$button->setText('To mesour.com >>');

		$button->setAttribute('href', $button->link('http://mesour.com'))
			->setAttribute('target', '_blank');

		return $button;
	}

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

	Mesour\UI\Filter::$maxCheckboxCount = 10;

	$grid = new \Mesour\UI\DataGrid('extendedDataGrid', $application);

	$wrapper = $grid->getWrapperPrototype();

	$wrapper->class('my-class');

	// TRUE = append
	$wrapper->class('my-next-class', true);

	/** @var \Mesour\DataGrid\Sources\IGridSource $source */
	$source = require_once __DIR__ . '/sources/' . $sourceFile . '.php';

	$dataStructure = $source->getDataStructure();

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

	for ($x = 0; $x < 8; $x++) {
		$sources[] = clone $source;
	}

	$grid->setSource($source);

	$pager = $grid->enablePager(8);

	$filter = $grid->enableFilter();

	$grid->onEditCell[] = function () {
		dump(func_get_args());
	};

	$subItems = $grid->enableSubItems();

	//$subItems->setPermission('menu', 'second');

	$subItems->addCallbackItem('test', 'Test callback item')
		//->setPermission('menu', 'second')
		->setCallback(function (User $user) {
			return $user->getName() . ' ' . $user->getSurname();
		});

	$i = 0;
	$subItems->addGridItem('groups', 'User groups', getSubGrid())
		//->setPermission('menu', 'second')
		->setCheckCallback(function (User $user, \Mesour\DataGrid\Extensions\SubItem\Items\Item $item) {
			/** @var \Mesour\Sources\Tests\Entity\User $user */
			if ($user->getId() == 1) {
				$item->setDisabled();
			} else {
				$item->setDisabled(false);
			}
		})
		->setCallback(function (\Mesour\UI\DataGrid $subGrid, $rowData) use ($sources, & $i) {
			$_source = $sources[$i];
			$subGrid->setSource($_source);
			$i++;
		});

	$subItems->addComponentItem('button', 'Component item', 'createTestButton')
		//->setPermission('menu', 'second')
		->setCallback(function (\Mesour\UI\Button $button, User $user) {

			$button->setText('Go to mesour.com from: ' . $user->getName() . ' ' . $user->getSurname() . ' >>');
			$button->setAttribute('href', $button->link('http://mesour.com', [
				'userId' => $user->getId(),
			]));
		});

	$templateItem = $subItems->addTemplateItem('description', 'Template item')
		//->setPermission('menu', 'second')
		->setCallback(function (\Mesour\UI\TemplateFile $template, User $user) {

			$template->name = $user->getName() . ' ' . $user->getSurname();
		});
	$templateItem->setTempDir(__DIR__ . '/temp');
	$templateItem->setFile(__DIR__ . '/test.latte');
	$templateItem->setBlock('test');

	$selection = $grid->enableRowSelection()
		->getLinks();

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

	$grid->enableExport(__DIR__ . '/temp');

	$grid->enableSortable('sort');

	$grid->onSort[] = function () {
		dump(func_get_args());
	};

	$status = $grid->addStatus('action', 'S');

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

	$grid->addImage('avatar', 'Avatar')
		->setPreviewPath('preview', __DIR__, __DIR__ . '/')
		->setMaxHeight(80)// translated as max-height: 80px;
		->setMaxWidth(80); // can use 80, "80px", "1em"...

	$container = $grid->addContainer('surname', 'Name')
		->setFiltering()
		->setOrdering();

	$container->addText('surname');

	$container->addText('name');

	$grid->addText('group', 'Group');

	$grid->addText('email', 'E-mail')
		->setOrdering(false);

	$grid->addNumber('amount', 'Amount')
		->setUnit('EUR');

	$grid->addDate($lastLogin, 'Last login')
		->setFormat('j.n.Y - H:i:s');

	$container = $grid->addContainer('blablablablablabla', 'Actions');

	$button = $container->addButton('test_button');

	$button->setIcon('pencil')
		->setType('primary')
		->setAttribute('href', $button->link('http://mesour.com'))
		->setAttribute('target', '_blank');

	$dropDown = $container->addDropDown('test_drop_down');

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

	//dump($_SESSION['Mesour\Components\Session']);

	$grid->render();

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