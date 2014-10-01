<?php

namespace DataGrid;

/**
 * Pager datagrid component
 */
class Pager extends \Nette\Application\UI\Control {

	const DEFAULT_LIMIT = 20;

	static public $advanced = FALSE;
	public $add_types = array('dialog');

	/**
	 * Create pager
	 * @param string $link
	 * @param numeric $total_count
	 * @param bool $advanced default: FALSE
	 */
	public function render($link, $total_count, $name, $advanced = NULL, $limit = 0) {
		if (is_null($advanced) === FALSE)
			self::$advanced = $advanced;
		$params = $this->presenter->getRequest()->getParameters();
		$param = isset($params[self::getParamName($link, $name)]) ? $params[self::getParamName($link, $name)] : NULL;
		$this->presenter->session->getSection('pager-' . $link . $name)->page =
			is_null($param) === TRUE && isset($this->presenter->session->getSection('pager-' . $link . $name)->page) === TRUE ? $this->presenter->session->getSection('pager-' . $link . $name)->page : (
			is_null($param) === FALSE ? $param : 0
			);
		$current_page = self::getCurrentPage($link, $name, $param);
		$this->template->link = $link;
		$this->template->name = $name;
		$this->template->total_count = $total_count;
		$limit = $limit > 0 ? $limit : self::DEFAULT_LIMIT;
		$this->template->pages_count = $total_count == 0 ? 0 : round(( $total_count + ( $limit - ($total_count % $limit))) / $limit, 0, PHP_ROUND_HALF_UP);
		$this->template->current_page = $current_page;
		if($this->presenter->isAjax())
			$this->presenter->redrawControl();
		$this->template->setFile(dirname(__FILE__) . '/templates/Pager.latte');
		$this->template->render();
	}

	static public function getCurrentPage($link, $name, $param = NULL) {
		$presenter = \Nette\Environment::getApplication()->getPresenter();
		return $presenter->session->getSection(self::getParamName($link, $name))->page =
			is_null($param) === TRUE && isset($presenter->session->getSection('pager-' . $link . $name)->page) === TRUE ? $presenter->session->getSection('pager-' . $link . $name)->page : (
			is_null($param) === FALSE ? $param : 0
			);
	}

	static public function getParamName($link, $name) {
		return 'page' . $link . $name;
	}

}