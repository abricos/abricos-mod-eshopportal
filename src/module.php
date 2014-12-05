<?php 
/**
 * @package Abricos
 * @subpackage Eshopportal
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin <roosit@abricos.org>
 */


/**
 * Модуль-сборка "Интернет-магазин"
 */
class EshopportalModule extends Ab_Module {
	
	/**
	 * Конструктор
	 */
	public function __construct(){
		$this->version = "0.1.2";
		$this->name = "eshopportal";
	}
}

Abricos::ModuleRegister(new EshopportalModule())

?>