<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class ExtensionStorage
{

	/**
	 * @var Mesour\DataGrid\Extensions\IExtension[]|BaseGrid
	 */
	private $parent;

	protected $extensions = [
		'IPager' => [
			'name' => 'pager',
			'interface' => Mesour\DataGrid\Extensions\Pager\IPager::class,
			'class' => Mesour\DataGrid\Extensions\Pager\PagerExtension::class,
		],
		'IFilter' => [
			'name' => 'filter',
			'interface' => Mesour\DataGrid\Extensions\Filter\IFilter::class,
			'class' => Mesour\DataGrid\Extensions\Filter\FilterExtension::class,
		],
		'ISortable' => [
			'name' => 'sortable',
			'interface' => Mesour\DataGrid\Extensions\Sortable\ISortable::class,
			'class' => Mesour\DataGrid\Extensions\Sortable\SortableExtension::class,
		],
		'ISelection' => [
			'name' => 'selection',
			'interface' => Mesour\DataGrid\Extensions\Selection\ISelection::class,
			'class' => Mesour\DataGrid\Extensions\Selection\SelectionExtension::class,
		],
		'IEditable' => [
			'name' => 'editable',
			'interface' => Mesour\DataGrid\Extensions\Editable\IEditable::class,
			'class' => Mesour\DataGrid\Extensions\Editable\EditableExtension::class,
		],
		'IOrdering' => [
			'name' => 'ordering',
			'interface' => Mesour\DataGrid\Extensions\Ordering\IOrdering::class,
			'class' => Mesour\DataGrid\Extensions\Ordering\OrderingExtension::class,
		],
		'IExport' => [
			'name' => 'export',
			'interface' => Mesour\DataGrid\Extensions\Export\IExport::class,
			'class' => Mesour\DataGrid\Extensions\Export\ExportExtension::class,
		],
		'ISubItem' => [
			'name' => 'sub_item',
			'interface' => Mesour\DataGrid\Extensions\SubItem\ISubItem::class,
			'class' => Mesour\DataGrid\Extensions\SubItem\SubItemExtension::class,
		],
	];

	public function __construct(BaseGrid $parent)
	{
		$this->setParent($parent);
	}

	public function setParent(BaseGrid $parent)
	{
		$this->parent = $parent;
	}

	public function addNewExtension($name, $componentName, $interface, $className)
	{
		if (isset($this->extensions[$name])) {
			throw new Mesour\InvalidStateException('Extension with name already exists.');
		}
		if (!interface_exists($interface)) {
			throw new Mesour\InvalidStateException('Interface "' . $interface . '" does not exists.');
		}
		if (!class_exists($className)) {
			throw new Mesour\InvalidStateException('Class "' . $className . '" does not exists.');
		}

		$checks = [
			'name' => [
				'value' => $componentName,
				'exception_name' => 'component name',
			],
			'interface' => [
				'value' => $interface,
			],
			'class' => [
				'value' => $className,
				'exception_name' => 'class name',
			],
		];

		foreach ($checks as $key => $check) {
			foreach ($this->extensions as $extension) {
				if ($extension[$key] === $check['value']) {
					throw new Mesour\InvalidStateException('Extension with "'
						. (isset($check['exception_name']) ? $check['exception_name'] : $key) . '" already exists.');
				}
			}
		}

		$this->extensions[$name] = [
			'name' => $componentName,
			'interface' => $interface,
			'class' => $className,
		];

		return $this;
	}

	/**
	 * @param Extensions\IExtension $extension
	 * @param string $extensionName
	 * @return Extensions\IExtension
	 * @throws Mesour\InvalidStateException
	 * @throws Mesour\InvalidArgumentException
	 */
	public function set(Extensions\IExtension $extension, $extensionName)
	{
		if (!isset($this->extensions[$extensionName])) {
			throw new Mesour\InvalidStateException('Extension name "' . $extensionName . '" does not exists.');
		} else {
			if (!$extension instanceof $this->extensions[$extensionName]['interface']) {
				throw new Mesour\InvalidArgumentException(
					'Extension must implement interface ' . $this->extensions[$extensionName]['interface'] . ' for extension "' . $extensionName . '".'
				);
			}
		}
		if ($extName = $this->getExtensionName($extension)) {
			if (strlen($extension->getName()) === 0) {
				throw new Mesour\InvalidStateException('Extension must have a set name.');
			}
			if (isset($this->parent[$extension->getName()])) {
				throw new Mesour\InvalidStateException('Extension with name "' . $extension->getName() . '" already exists.');
			}
			$this->parent->addComponent($extension);

			/** @var Extensions\IExtension $extension */
			$extension->createInstance($extension, $extensionName);

			$this->extensions[$extName]['name'] = $extension->getName();

			return $extension;
		} else {
			throw new Mesour\OutOfRangeException('Trying to set unknown extension.');
		}
	}

	public function get($extensionName, $need = true)
	{
		if (!is_string($extensionName)) {
			throw new Mesour\InvalidArgumentException('Extension must be string.');
		}
		if (isset($this->extensions[$extensionName])) {
			$name = $this->extensions[$extensionName]['name'];
			$class = $this->extensions[$extensionName]['class'];
			$extension = $this->parent->getComponent($name, false);
			if (is_null($extension) && $need) {
				$instance = $this->set(new $class($name), $extensionName);
				if (!isset($this->parent[$name])) {
					$this->parent->addComponent($instance);
				}
			} elseif (is_null($extension)) {
				return null;
			}
			return $this->parent[$name];
		} else {
			if ($need) {
				throw new Mesour\OutOfRangeException('Trying to get unknown extension.');
			} else {
				return null;
			}
		}
	}

	/**
	 * @return Extensions\IExtension[]
	 */
	public function getActiveExtensions()
	{
		$output = [];
		foreach ($this->extensions as $interface => $option) {
			if (
				isset($this->parent[$option['name']])
				&& !$this->parent[$option['name']]->isDisabled()
				&& $this->parent[$option['name']]->isAllowed()
			) {
				$output[$option['name']] = $this->parent[$option['name']];
			}
		}
		return $output;
	}

	/**
	 * @param Extensions\IExtension $extension
	 * @return string
	 */
	private function getExtensionName(Extensions\IExtension $extension)
	{
		foreach ($this->extensions as $name => $ext) {
			if (is_subclass_of($extension, $ext['interface'])) {
				return $name;
			}
		}
		return false;
	}

}