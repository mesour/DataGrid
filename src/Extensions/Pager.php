<?php

namespace Mesour\DataGrid\Extensions;

use \Nette\Application\UI\Form;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Pager extends BaseControl {

	/**
	 * @persistent
	 */
	public $number;

	/**
	 * @var \Nette\Utils\Paginator
	 */
	private $paginator;

	private $max_for_normal = 15;

	private $edge_page_count = 3;

	private $middle_page_count = 2;

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
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
		if (!isset($this->settings['page'])) {
			$this->settings['page'] = 1;
		}
		if ($this->settings['page'] > $this->paginator->getPageCount()) {
			$this->settings['page'] = $this->paginator->getPageCount();
		}
		$this->paginator->setPage(isset($this->settings['page']) ? $this->settings['page'] : 1);
	}

	public function getPaginator() {
		return $this->paginator;
	}

	public function reset() {
		$this->settings['page'] = 0;
	}

	/**
	 * Create pager
	 */
	public function render() {
		$this->template->paginator = $this->paginator;
		$this->template->max_for_normal = $this->max_for_normal;
		$this->template->edge_page_count = $this->edge_page_count;
		$this->template->middle_page_count = $this->middle_page_count;
		$this->template->grid_dir = __DIR__;

		if ($this->paginator->getPageCount() > $this->max_for_normal) {
			$this->template->setFile(dirname(__FILE__) . '/templates/Pager/PagerAdvanced.latte');
		} else {
			$this->template->setFile(dirname(__FILE__) . '/templates/Pager/Pager.latte');
		}
		$this->template->render();
	}

	public function handleChangePage() {
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
		$form->setTranslator($this->parent["translator"]);

		$form->getElementPrototype()
		    ->action($this->link('toPage'));

		$form->addText('number')
		    ->setAttribute('placeholder', 'Page');

		$form->addSubmit('to_page', 'Go!');

		return $form;
	}

	public function handleToPage() {

		$number = (int)trim($this->number);
		if ($number <= 0) {
			$number = 1;
		}
		$this->settings['page'] = $number;
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}