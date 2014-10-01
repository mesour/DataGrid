<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\DateColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class DateColumn extends BaseOrderingColumn {

	/**
	 * Possible option key
	 */
	const FORMAT = 'format';

	/**
	 * @param \Nette\Application\UI\Presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter, array $option = array()) {
		parent::__construct($presenter, $option);
	}

	public function setFormat($format) {
		$this->option[self::FORMAT] = $format;
		return $this;
	}

	/**
	 * Create HTML header
	 * 
	 * @return \Nette\Utils\Html
	 * @throws \DataGrid\Grid_Exception
	 */
	public function createHeader() {
		parent::createHeader();
		$th = Html::el('th');
		if (array_key_exists(self::TEXT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DateColumn::TEXT is required.');
		}
		if (array_key_exists(self::FORMAT, $this->option) === FALSE) {
			throw new Grid_Exception('Option \DataGrid\DateColumn::FORMAT is required.');
		}
		$this->addHeaderOrdering($th);
		return $th;
	}

	/**
	 * Create HTML body
	 *
	 * @param mixed $data
	 * @param string $container
	 * @return Html|void
	 * @throws Grid_Exception
	 */
	public function createBody($data, $container = 'td') {
		parent::createBody($data);

		$span = Html::el($container);
		if (isset($this->data[$this->option[self::ID]]) === FALSE) {
			throw new Grid_Exception('Column ' . $this->option[self::ID] . ' does not exists in DataSource.');
		}

		if(is_numeric($this->data[$this->option[self::ID]])) {
			$date = new \DateTime();
			$date->setTimestamp($this->data[$this->option[self::ID]]);
		} else {
			$date = new \DateTime($this->data[$this->option[self::ID]]);
		}

		$span->setText($date->format($this->option[self::FORMAT]));
		return $span;
	}

}