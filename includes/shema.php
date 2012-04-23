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


function EshoportalUploadImage($file){
	$modFileManager = Abricos::GetModule('filemanager');
	if (empty($modFileManager)){ return ''; }
	
	$uploadFile = FileManagerModule::$instance->GetManager()->CreateUpload($file);
	$uploadFile->ignoreUploadRole = true;
	$uploadFile->ignoreFileSize = true;
	$uploadFile->isOnlyImage = true;
	$uploadFile->folderPath = "eshop/".date("d.m.Y", TIMENOW);
	
	$errornum = $uploadFile->Upload();

	if (empty($errornum)){
		return $uploadFile->uploadFileHash;
	}
	return '';
}

if (Ab_UpdateManager::$isCoreInstall){ 
	// разворачиваем коробку при инсталляции платформы
	
	$devMode = Abricos::$config['Misc']['develop_mode'];
	
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
	
	$modFileManager = Abricos::GetModule('filemanager');
	
	// Интернет-магазин
	$modEshop = Abricos::GetModule('eshop');
	if (!empty($modEshop) && !empty($modFileManager)){
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
			
			$ordwg = 100;
			
			$cat = new stdClass();
			$cat->img = EshoportalUploadImage(CWD.'/modules/eshopportal/mediasrc/tv.jpg');
			$cat->nm = 'tv';
			$cat->tl = 'Телевизоры';
			$cat->ord = $ordwg--;
			$cat->dsc = "
				<p>
					В магазине бытовой техники и электроники Абрикос-Show Вы можете приобрести 
					телевизор онлайн, подобрав модель телевизора на свой вкус и в зависимости от 
					потребностей.
 				</p>
			";
			$pcatid = $manCatalog->CatalogAppend($cat);

			$cat = new stdClass();
			$cat->img = EshoportalUploadImage(CWD.'/modules/eshopportal/mediasrc/tvlcd.jpg');
			$cat->pid = $pcatid;
			$cat->nm = 'tvlcd';
			$cat->tl = 'ЖК-телевизоры';
			$cat->ord = $ordwg--;
			$cat->dsc = "
			<p>
				ЖК (жидко-кристалические) телевизоры  - это отличная передача звука и качества. 
				Уже давно пора давно забыть об ЭЛТ телевизорах и купить телевизор жк. 
				В нашем магазине вы сможете подобрать то, что вам нужно. 
			</p>
			";
			$catid = $manCatalog->CatalogAppend($cat);
				
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "ЖК-телевизор Philips 19PFL3606H/60";
			$p->fld_art = "8546811";
			$p->fld_sklad = "3";
			$p->fld_price = "7230";
			$manCatalog->ElementAppend($p);

			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №2 Р1";
			$p->fld_art = "8546812";
			$p->fld_sklad = "7";
			$p->fld_price = "1500.75";
			$manCatalog->ElementAppend($p);

			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №3 Р1";
			$p->fld_art = "8546813";
			$p->fld_sklad = "0";
			$p->fld_price = "399.90";
			$manCatalog->ElementAppend($p);

			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №4 Р1";
			$p->fld_art = "8546814";
			$p->fld_sklad = "4";
			$p->fld_price = "259.90";
			$manCatalog->ElementAppend($p);

			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "№5 Р1";
			$p->fld_art = "8546815";
			$p->fld_sklad = "9";
			$p->fld_price = "59.90";
			$manCatalog->ElementAppend($p);
			
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №6 Р1";
			$p->fld_art = "8546816";
			$p->fld_sklad = "4";
			$p->fld_price = "259.90";
			$manCatalog->ElementAppend($p);
			
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №7 Р1";
			$p->fld_art = "8546817";
			$p->fld_sklad = "0";
			$p->fld_price = "399.90";
			$manCatalog->ElementAppend($p);
			
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар №8 Р1";
			$p->fld_art = "8546818";
			$p->fld_sklad = "4";
			$p->fld_price = "259.90";
			$manCatalog->ElementAppend($p);
			
			$cat = new stdClass();
			$cat->img = EshoportalUploadImage(CWD.'/modules/eshopportal/mediasrc/tvkinescope.jpg');
			$cat->pid = $pcatid;
			$cat->nm = 'tvkinescope';
			$cat->tl = 'Кинескопные телевизоры';
			$cat->ord = $ordwg--;
			$cat->dsc = "
			<p>
				Очень большие и тяжелые телевизоры прошлого века. К тому же потребляют 
				существенное кол-во электроэнергии. 
			</p>
			";
			$catid = $manCatalog->CatalogAppend($cat);
				
			
			if ($devMode){
				$pcatid = $pcatid;
				
				// Подраздел
				$cat = new stdClass();
				$cat->pid = $pcatid;
				$cat->nm = 'porazdel11';
				$cat->tl = 'Подаздел №1 Р1';
				$cat->dsc = "";
				$catid = $manCatalog->CatalogAppend($cat);
				
				// Товары подраздела
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №1 П1 Р1";
				$p->fld_art = "8546811";
				$p->fld_sklad = "15";
				$p->fld_price = "1700";
				$manCatalog->ElementAppend($p);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №2 П1 Р1";
				$p->fld_art = "8546812";
				$p->fld_sklad = "7";
				$p->fld_price = "1500.75";
				$manCatalog->ElementAppend($p);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №3 П1 Р1";
				$p->fld_art = "8546813";
				$p->fld_sklad = "0";
				$p->fld_price = "399.90";
				$manCatalog->ElementAppend($p);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №4 П1 Р1";
				$p->fld_art = "8546814";
				$p->fld_sklad = "4";
				$p->fld_price = "259.90";
				$manCatalog->ElementAppend($p);
				
				// Раздел 12
				$cat = new stdClass();
				$cat->pid = $pcatid;
				$cat->nm = 'razdel12';
				$cat->tl = 'Подаздел №2 Р1';
				$cat->dsc = "";
				$catid = $manCatalog->CatalogAppend($cat);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №1 П2 Р1";
				$p->fld_art = "8546813";
				$p->fld_sklad = "0";
				$p->fld_price = "399.90";
				$manCatalog->ElementAppend($p);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №2 П2 Р1";
				$p->fld_art = "8546814";
				$p->fld_sklad = "4";
				$p->fld_price = "259.90";
				$manCatalog->ElementAppend($p);
				
				$p = new stdClass();
				$p->catid = $catid;
				$p->fld_name = "Товар №2 П2 Р1";
				$p->fld_art = "8546814";
				$p->fld_sklad = "4";
				$p->fld_price = "259.90";
				$manCatalog->ElementAppend($p);
				
				// Раздел 13
				$cat = new stdClass();
				$cat->pid = $pcatid;
				$cat->nm = 'razdel13';
				$cat->tl = 'Подаздел №3 Р1';
				$cat->dsc = "";
				$catid = $manCatalog->CatalogAppend($cat);

				// Раздел 14
				$cat = new stdClass();
				$cat->pid = $pcatid;
				$cat->nm = 'razdel14';
				$cat->tl = 'Подаздел №4 Р1';
				$cat->dsc = "";
				$catid = $manCatalog->CatalogAppend($cat);
			}
			
			// Раздел 2
			$cat = new stdClass();
			$cat->ord = $ordwg--;
			$cat->nm = 'razdel2';
			$cat->tl = 'Раздел №2';
			$cat->dsc = "
				<p>
					Новинки нашего интернет-магазина в в разделе 2.
				</p>
			";
			$catid = $manCatalog->CatalogAppend($cat);
				
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар Р21";
			$p->fld_art = "8546854";
			$p->fld_sklad = "0";
			$p->fld_price = "920.5";
			$manCatalog->ElementAppend($p);
			
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар Р22";
			$p->fld_art = "8546854";
			$p->fld_sklad = "1";
			$p->fld_price = "1270.75";
			$manCatalog->ElementAppend($p);
			
			$p = new stdClass();
			$p->catid = $catid;
			$p->fld_name = "Товар Р23";
			$p->fld_art = "8546854";
			$p->fld_sklad = "28";
			$p->fld_price = "99.90";
			$manCatalog->ElementAppend($p);
			
			
			if ($devMode){
				$cat = new stdClass();
				$cat->ord = $ordwg--;
				$cat->nm = 'razdel3';
				$cat->tl = 'Раздел №3';
				$cat->dsc = "
					<p>
						Подробное описание раздела номер три каталога продукции 
						интернет-магазина Абрикос-Шоп.
					</p>
				";
				$catid = $manCatalog->CatalogAppend($cat);
				
				$cat = new stdClass();
				$cat->ord = $ordwg--;
				$cat->nm = 'razdel4';
				$cat->tl = 'Раздел №4';
				$cat->dsc = "
				<p>
					Подробное описание раздела номер четыре.
				</p>
				";
				$catid = $manCatalog->CatalogAppend($cat);
				
				$cat = new stdClass();
				$cat->ord = $ordwg--;
				$cat->nm = 'razdel5';
				$cat->tl = 'Раздел №5';
				$cat->dsc = "
				<p>
					Подробное описание раздела номер пять.
				</p>
				";
				$catid = $manCatalog->CatalogAppend($cat);
				
			}
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