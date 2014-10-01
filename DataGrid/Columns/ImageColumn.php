<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter;

/**
 * Description of \DataGrid\ImageColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class ImageColumn extends BaseColumn {

	/**
	 * Possible option key
	 */
	const ID = 'id',
	    TEXT = 'text',
	    MAX_WIDTH = 'ordering',
	    MAX_HEIGHT = 'function';

	/**
	 * @param \Nette\Application\UI\Presenter
	 * @param array $option
	 */
	public function __construct(Presenter $presenter, array $option = array()) {
		parent::__construct($presenter, $option);
	}

	public function setId($id) {
		$this->option[self::ID] = $id;
		return $this;
	}

	public function setText($text) {
		$this->option[self::TEXT] = $text;
		return $this;
	}

	public function setMaxWidth($max_width) {
		$this->option[self::MAX_WIDTH] = $max_width;
		return $this;
	}

	public function setMaxHeight($max_height) {
		$this->option[self::MAX_HEIGHT] = $max_height;
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
			throw new Grid_Exception('Option \DataGrid\ImageColumn::TEXT is required.');
		}
		$th->setText($this->option[self::TEXT]);
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

		$img = Html::el('img', array('src' => $this->data[$this->option[self::ID]]));
		if(isset($this->option[self::MAX_WIDTH]) === TRUE) {
			$img->style('max-width:'.self::fixPixels($this->option[self::MAX_WIDTH]), TRUE);
		}
		if(isset($this->option[self::MAX_HEIGHT]) === TRUE) {
			$img->style('max-height:'.self::fixPixels($this->option[self::MAX_HEIGHT]), TRUE);
		}
		$span->add($img);
		return $span;
	}

	static private function fixPixels($value) {
		return is_numeric($value) ? ($value . 'px') : $value;
	}

}