<?php

use Mesour\DataGrid\NetteDbDataSource;
use Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class NetteDataSource extends \Test\DataSourceTestCase
{

    CONST FULL_USER_COUNT = 20;

    /**
     * @var \Nette\Database\Context
     */
    private $db;

    public function __construct(Nette\DI\Container $container)
    {
        parent::__construct($container);
        $this->db = $this->getByType('Nette\Database\Context');
    }

    public function testTotalCount()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchTotalCount($source);
    }

    public function testLimit()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchLimit($source);
    }

    public function testOffset()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchOffset($source);
    }

    public function testWhere()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $source->where('action = ?', 1);

        $this->matchWhere($source);
    }

    public function testEmpty()
    {
        $source = new NetteDbDataSource($this->db->table('empty'));

        $this->matchEmpty($source);
    }

    public function testCheckers()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchCheckers($source);
    }

    public function testCustom()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchCustom($source);
    }

    public function testCustomOr()
    {
        $source = new NetteDbDataSource($this->db->table('user'));

        $this->matchCustomOr($source);
    }

    public function testRelated()
    {
        $source = new NetteDbDataSource(
            $this->db->table('user')
                ->select('user_id,user.name,action,surname,email')
                ->select('last_login,amount,avatar,order,timestamp,group_id'),
            $this->db
        );

        Assert::false($source->isRelated('group'));

        $source->setRelated('group', 'group_id', 'name', 'group_name');

        Assert::true($source->isRelated('group'));

        $related = $source->related('group');

        Assert::type('Mesour\DataGrid\NetteDbDataSource', $related);
        Assert::same(self::GROUPS_COUNT, $related->getTotalCount());
        Assert::same(count($source->fetch()), self::COLUMN_COUNT + 1); // + 1 because using related (group_name column)
    }

}

$test = new NetteDataSource($container);
$test->run();