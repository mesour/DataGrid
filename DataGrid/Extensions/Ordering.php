<?php

namespace DataGrid\Extensions;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Ordering extends BaseControl {

	private $default_order = array();

	private $disabled = FALSE;

	private $multi = FALSE;

	public function setDefaultOrder($key, $sorting = 'ASC') {
		$this->default_order = array($key, $sorting);
	}

	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
	}

	public function isDisabled() {
		return $this->disabled;
	}

	public function enableMulti() {
		$this->multi = TRUE;
	}

	/**
	 * Get ordering for column by column ID
	 *
	 * @param $column_id
	 * @return NULL|ASC|DESC
	 */
	public function getOrdering($column_id) {
		if(!empty($this->default_order) && empty($this->settings['ordering']) && $this->default_order[0] === $column_id) {
			return $this->default_order[1];
		}
		if (!isset($this->settings['ordering']) || !isset($this->settings['ordering'][$column_id])) {
			return NULL;
		} else {
			return $this->settings['ordering'][$column_id];
		}
	}

	public function applyOrder() {
		if (isset($this->settings['ordering']) && !empty($this->settings['ordering'])) {
			foreach ($this->settings['ordering'] as $key => $how_to_order) {
				if (!in_array($key, $this->parent->getRealColumnNames())) {
					unset($this->settings['ordering'][$key]);
				} else {
					$this->parent->getDataSource()->orderBy($key, $how_to_order);
				}
			}
		}
		if(empty($this->settings['ordering']) && !empty($this->default_order)) {
			$this->parent->getDataSource()->orderBy($this->default_order[0], $this->default_order[1]);
		}

	}

	public function handleOrdering($column_id) {
		if (!isset($this->settings['ordering'])) {
			$this->settings['ordering'] = array();
		}
		if (!isset($this->settings['ordering'][$column_id])) {
			$this->settings['ordering'][$column_id] = 'ASC';
		} elseif ($this->settings['ordering'][$column_id] === 'ASC') {
			$this->settings['ordering'][$column_id] = 'DESC';
		} else {
			unset($this->settings['ordering'][$column_id]);
		}
		if(!$this->multi) {
			$current = isset($this->settings['ordering'][$column_id]) ? $this->settings['ordering'][$column_id] : NULL;
			if(!is_null($current)) {
				$this->settings['ordering'] = array();
				$this->settings['ordering'][$column_id] = $current;
			}
		}

		$this->getSession()->settings = $this->settings;
		$this->parent->redrawControl();
		$this->presenter->redrawControl();
	}

}