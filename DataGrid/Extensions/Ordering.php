<?php

namespace DataGrid\Extensions;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
class Ordering extends BaseControl {

	/**
	 * Get ordering for column by column ID
	 *
	 * @param $column_id
	 * @return null|ASC|DESC
	 */
	public function getOrdering($column_id) {
		if (!isset($this->settings['ordering']) || !isset($this->settings['ordering'][$column_id])) {
			return NULL;
		} else {
			return $this->settings['ordering'][$column_id];
		}
	}

	public function applyOrder() {
		if (isset($this->settings['ordering']) && !empty($this->settings['ordering'])) {
			$data = array_keys($this->parent->getDataSource()->fetch());
			foreach ($this->settings['ordering'] as $key => $value) {
				if (!in_array($key, $data)) {
					unset($this->settings->ordering[$key]);
				}
			}

			foreach ($this->settings['ordering'] as $key => $how_to_order) {
				$this->parent->getDataSource()->orderBy($key, $how_to_order);
			}
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

		$this->getSession()->settings = $this->settings;

		$this->parent->redrawControl();
	}

}