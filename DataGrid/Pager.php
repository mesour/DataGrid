<?php

namespace DataGrid;

/**
 * Pager datagrid component
 */
class Pager extends \Nette\Application\UI\Control {

	/**
	 * Private session section
	 *
	 * @var \Nette\Http\SessionSection
	 */
	private $private_session;

	/**
	 * @var \Nette\Utils\Paginator
	 */
	private $paginator;

	/**
	 * @param string $session_prefix
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param null|string $name
	 */
	public function __construct($session_prefix, \Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->private_session = $this->presenter->getSession()->getSection($session_prefix . $name);
		if(!$this->private_session->page) {
			$this->private_session->page = 1;
		}
		$this->paginator = new \Nette\Utils\Paginator;
	}

	/**
	 * @param numeric $total_count
	 * @param numeric $limit
	 */
	public function setCounts($total_count, $limit) {
		$this->paginator->setItemCount($total_count);
		$this->paginator->setItemsPerPage($limit);
		$this->paginator->setPage($this->private_session->page);
	}

	/**
	 * Create pager
	 */
	public function render() {
		$this->template->paginator = $this->paginator;

		$this->template->setFile(dirname(__FILE__) . '/templates/Pager.latte');
		$this->template->render();
	}

	public function handleChangePage($page) {
		$this->private_session->page = $page;
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

	/**
	 * Returns current page index
	 *
	 * @return int
	 */
	public function getCurrentPageIndex() {
		return $this->paginator->getPage() - 1;
	}

}