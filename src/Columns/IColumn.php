<?php

namespace Mesour\DataGrid\Column;

use \Nette\Localization\ITranslator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
interface IColumn {

	public function getId();

	public function getHeader();

	public function isEditable();

	public function hasFiltering();

	public function getHeaderAttributes();

	public function getHeaderContent();

	public function getBodyAttributes($data);

	public function getBodyContent($data);

	public function setTranslator(ITranslator $translator);
}