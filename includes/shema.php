<?php
/**
 * Схема таблиц данного модуля
 * 
 * @version $Id$
 * @package Abricos
 * @subpackage Eshopportal
 * @copyright Copyright (C) 2012 Abricos. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

$charset = "CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'";
$updateManager = Ab_UpdateManager::$current; 
$db = Abricos::$db;
$pfx = $db->prefix;

if (Ab_UpdateManager::$isCoreInstall){ 
	// разворачиваем коробку при инсталляции платформы
	
	Abricos::$user->id = 1;
	
	// Установить шаблон gov
	Abricos::GetModule('sys')->GetManager();
	$sysMan = Ab_CoreSystemManager::$instance;
	$sysMan->DisableRoles();
	$sysMan->SetTemplate('eshoptp');
	$sysMan->SetSiteName('Абрикос Shop');
	$sysMan->SetSiteTitle('современный интернет-магазин');
	
	
	// Страницы сайта
	Abricos::GetModule('sitemap')->GetManager();
	$manSitemap = SitemapManager::$instance;
	$manSitemap->DisableRoles();
	$manSitemap->MenuRemove(2);
	
	$ord = 10;
	
	// Интернет-магазин
	$modEshop = Abricos::GetModule('eshop');
	if (!empty($modEshop)){
		$m = new stdClass();
		$m->nm = 'eshop';
		$m->tl = 'Интернет-магазин';
		$m->ord = $ord++;
		$m->id = $manSitemap->MenuAppend($m);
	
		$p = new stdClass();
		$p->mid = $m->id;
		$p->nm = 'index';
		$p->bd = '';
		$manSitemap->PageAppend($p);
		
		// Создание разделов
		$modCatalog = Abricos::GetModule('catalog');
		if (!empty($modCatalog)){
			$modCatalog->GetManager();
			$manCatalog = CatalogManager::$instance;
			
			$cat = new stdClass();
			$cat->nm = 'razdel1';
			$cat->tl = 'Раздел 1';
			$cat->dsc = "
				<p>
					Предлагаем Вашему вниманию товары из раздела 1. 
					Товары раздела 1 это самые эффективные товары на сегдняшний день.
				</p>
			";
			$manCatalog->CatalogAppend($cat);
		}
	}
	
	// Контакты
	$m = new stdClass();
	$m->nm = 'contacts';
	$m->tl = 'Контакты';
	$m->ord = $ord++;
	$m->id = $manSitemap->MenuAppend($m);
	
	$p = new stdClass();
	$p->mid = $m->id;
	$p->nm = 'index';
	$p->bd = "
		<h2>Контакты</h2>
		
		<p>
			Компания <i>Абрикос Shop</i>
		</p>
		
		<p>101000, г.Москва, Красная площадь, дом 1</p>
		
		<p>
			Тел.: 101-00-01<br>
			Факс. 101-00-02
		</p>
	";
	$manSitemap->PageAppend($p);
	
	Abricos::$user->id = 0;
}




?>