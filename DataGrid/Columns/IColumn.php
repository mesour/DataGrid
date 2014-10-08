<?php

namespace DataGrid\Column;

/**
 * Description of \DataGrid\Column\IColumn
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
interface IColumn {

	public function getId();

	public function getText();

	public function isEditable();

	public function getHeaderAttributes();

	public function getHeaderContent();

	public function getBodyAttributes($data);

	public function getBodyContent($data);

} 