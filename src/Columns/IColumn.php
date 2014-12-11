<?php

namespace DataGrid\Column;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour DataGrid
 */
interface IColumn {

	public function getId();

	public function getText();

	public function isEditable();

	public function hasFiltering();

	public function getHeaderAttributes();

	public function getHeaderContent();

	public function getBodyAttributes($data);

	public function getBodyContent($data);

	public function setTranslator(\Nette\Localization\ITranslator $translator);
}