<?php

namespace DataGrid;

use \Nette\Application\UI\Form;

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

	private $max_for_normal = 15;

	private $edge_page_count = 3;

	private $middle_page_count = 2;

	/**
	 * @param string $session_prefix
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param null|string $name
	 */
	public function __construct($session_prefix, \Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->private_session = $this->presenter->getSession()->getSection($session_prefix . $name);
		$this->paginator = new \Nette\Utils\Paginator;
	}

	public function setMaxForNormal($max_for_normal) {
		$this->max_for_normal = $max_for_normal;
		return $this;
	}

	public function setEdgePageCount($edge_page_count) {
		$this->edge_page_count = $edge_page_count;
		return $this;
	}

	public function setMiddlePageCount($middle_page_count) {
		$this->middle_page_count = $middle_page_count;
		return $this;
	}

	/**
	 * @param numeric $total_count
	 * @param numeric $limit
	 */
	public function setCounts($total_count, $limit) {
		$this->paginator->setItemCount($total_count);
		$this->paginator->setItemsPerPage($limit);
		if($this->private_session->page > $this->paginator->getPageCount()) {
			$this->private_session->page = $this->paginator->getPageCount();
		}
		$this->paginator->setPage(isset($this->private_session->page) ? $this->private_session->page : 1);
	}

	/**
	 * Create pager
	 */
	public function render() {
		$this->template->paginator = $this->paginator;
		$this->template->max_for_normal = $this->max_for_normal;
		$this->template->edge_page_count = $this->edge_page_count;
		$this->template->middle_page_count = $this->middle_page_count;

		if($this->paginator->getPageCount() > $this->max_for_normal) {
			$this->template->setFile(dirname(__FILE__) . '/templates/PagerAdvanced.latte');
		} else {
			$this->template->setFile(dirname(__FILE__) . '/templates/Pager.latte');
		}
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

	protected function createComponentPageForm() {
		$form = new Form;

		$form->getElementPrototype()
			->action($this->link('toPage'));

		$form->addText('number')
			->setAttribute('placeholder', 'Page');

		$form->addSubmit('to_page', 'Go!');

		return $form;
	}

	public function handleToPage() {
		$values = $this->parent->getHttpRequest()->getPost();
		$number = (int) trim($values['number']);
		if($number <= 0) {
			$number = 1;
		}
		$this->private_session->page = $number;
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}