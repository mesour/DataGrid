<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Extensions\Ordering;

use Mesour;

/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class OrderingExtension extends Mesour\DataGrid\Extensions\Base implements IOrdering
{

	private $defaultOrder = [];

	private $disabled = false;

	private $multi = false;

	private $ordering = [];

	/** @var Mesour\Components\Session\ISessionSection */
	private $privateSession;

	public function attached(Mesour\Components\ComponentModel\IContainer $parent)
	{
		parent::attached($parent);
		$this->privateSession = $this->getSession()->getSection($this->createLinkName());
		$this->ordering = $this->privateSession->get('ordering', []);
		return $this;
	}

	public function setDefaultOrder($key, $sorting = 'ASC')
	{
		$this->defaultOrder = [$key, $sorting];
	}

	public function setDisabled($disabled = true)
	{
		$this->disabled = $disabled;
	}

	public function isDisabled()
	{
		return $this->disabled;
	}

	public function enableMulti()
	{
		$this->multi = true;
	}

	/**
	 * Get ordering for column by column ID
	 * @param int $columnId
	 * @return NULL|string(ASC|DESC)
	 */
	public function getOrdering($columnId)
	{
		if (count($this->defaultOrder) > 0
			&& $this->ordering === 0
			&& $this->defaultOrder[0] === $columnId
		) {
			return $this->defaultOrder[1];
		}
		if (!isset($this->ordering[$columnId])) {
			return null;
		} else {
			return $this->ordering[$columnId];
		}
	}

	/**
	 * @return Mesour\DataGrid\ExtendedGrid
	 */
	public function getGrid()
	{
		return $this->getParent();
	}

	public function applyOrder()
	{
		$c = count($this->ordering);
		if ($c > 0) {
			foreach ($this->ordering as $key => $howToOrder) {
				if (!in_array($key, $this->getGrid()->getRealColumnNames())) {
					unset($this->ordering[$key]);
				} else {
					$this->getGrid()->getSource()->orderBy($key, $howToOrder);
				}
			}
		}
		if ($c === 0 && count($this->defaultOrder) > 0) {
			$this->getGrid()->getSource()
				->orderBy($this->defaultOrder[0], $this->defaultOrder[1]);
		}
	}

	public function reset($hard = false)
	{
		if ($hard) {
			$this->ordering = [];
			$this->privateSession->get('set', $this->ordering);
		}
	}

	public function handleOrdering($key)
	{
		$pager = $this->getGrid()->getComponent('pager', false);
		if ($pager instanceof Mesour\DataGrid\Extensions\Pager\IPager) {
			$pager->reset();
		}

		if (!isset($this->ordering[$key])) {
			$this->ordering[$key] = 'ASC';
		} elseif ($this->ordering[$key] === 'ASC') {
			$this->ordering[$key] = 'DESC';
		} else {
			unset($this->ordering[$key]);
		}
		if (!$this->multi) {
			$current = isset($this->ordering[$key]) ? $this->ordering[$key] : null;
			if (!is_null($current)) {
				$this->ordering = [];
				$this->ordering[$key] = $current;
			}
		}
		$this->privateSession->set('ordering', $this->ordering);
	}

	public function beforeFetchData($data = [])
	{
		$this->applyOrder();
	}

	public function afterGetCount($count)
	{
		$this->create();
		foreach ($this->getGrid()->getColumns() as $column) {
			if ($column instanceof Mesour\DataGrid\Column\IOrdering
				&& $this->getGrid()->getExtension('IOrdering')->isDisabled()
			) {
				$column->setOrdering(false);
			}
		}
	}

}
