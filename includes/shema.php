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
	if (!file_exists($file) || empty($modFileManager)){ return ''; }
	
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

class EshopportalTempClass {
	public static $ordw = 1000;
}

function EshopportalCatalogAppend($parentid, $name, $title, $desc = ''){
	$cat = new stdClass();
	$cat->nm = $name;
	$cat->tl = $title;
	$cat->pid = $parentid;
	$cat->ord = EshopportalTempClass::$ordw--;
	$cat->img = EshoportalUploadImage(CWD.'/modules/eshopportal/mediasrc/'.$name.'.jpg');
	$cat->dsc = $desc;
	$manCatalog = EShopModule::$instance->GetCatalogManager();
	return $manCatalog->CatalogAppend($cat);
}

function EshopportalElementAppend($catid, $title, $imgs="", $desc='', $overfld = array()){
	$p = new stdClass();
	
	$p->catid = $catid;
	$p->fld_name = $title;
	$p->fld_art = rand(100000, 999999);
	$p->fld_sklad = rand(0, 50);
	$p->fld_price = rand (100, 10000)+rand (0, 99)*0.1;
	$p->fld_desc = $desc;
	
	if (is_array($overfld) && count($overfld)>0){
		foreach($overfld as $fld => $val){
			$fname = "fld_".$fld;
			$p->$fname = $val;
		}
	}
	
	$manCatalog = EShopModule::$instance->GetCatalogManager();
	

	$elid = $manCatalog->ElementAppend($p);
	
	if (empty($elid)){ return 0; }
	
	if (is_string($imgs)){
		$imgs = explode(",", $imgs);
	}
	if (is_array($imgs)){
		$afids = array();
		foreach ($imgs as $img){
			$fid = EshoportalUploadImage(CWD.'/modules/eshopportal/mediasrc/'.$img.'.jpg');
			if (!empty($fid)){
				array_push($afids, $fid);
			}
		}
		if (count($afids)>0){
			CatalogQuery::FotoAppend(Abricos::$db, $elid, $afids);
		}
	}
	
	return $elid;
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
			
			// Телевизоры
			$pcatid = EshopportalCatalogAppend(0, 'tv', 'Телевизоры', "
				<p>
					В магазине бытовой техники и электроники Абрикос-Show Вы можете приобрести 
					телевизор онлайн, подобрав модель телевизора на свой вкус и в зависимости от 
					потребностей.
 				</p>
			");

			$catid = EshopportalCatalogAppend($pcatid, 'tvlcd', 'ЖК-телевизоры', "
				<p>
					ЖК (жидко-кристалические) телевизоры  - это отличная передача звука и качества. 
					Уже давно пора давно забыть об ЭЛТ телевизорах и купить телевизор жк. 
					В нашем магазине вы сможете подобрать то, что вам нужно. 
				</p>
			");
			EshopportalElementAppend($catid, "ЖК-телевизор Philips 19PFL3606H/60", "tvlcd001-1,tvlcd001-2,tvlcd001-3,tvlcd001-4,tvlcd001-5,tvlcd001-6", "
				<p>
					С Philips вы можете наслаждаться великолепным качеством телевизора по разумной цене — сегодня и всегда. 
					Этот ЖК-телевизор модели 19PFL3606 серии 3000 обеспечивает высокое качество изображения, имеет удобные 
					разъемы для цифрового подключения и отличается прекрасным дизайном.
				</p>
			");
			EshopportalElementAppend($catid, "ЖК-телевизор Samsung LE-19D450G1W", "tvlcd002-1", "
				<p>
					Видео:<br>- Разрешение: 1366 x 768<br>- Технология 50 Clear Motion Rate<br>
					- Процессор: DNIe+ Picture Engine (высокий контраст)<br>
					- Технология Широкоуг. Color Enhancer Plus A8123 (Улучшение цвета)<br>
					Звук:<br>- Dolby Digital Plus, Dolby Pulse<br>- Звук: SRS Theater Sound<br>
					- DTS 2.0 + цифровой выход<br>Особенности:<br>- AnyNet+ HDMI-CEC <br>
					- Автопоиск каналов<br>- Гид по программам (EPG)<br>- Телетекст (TTXT) <br>
					- Язык меню (29 европейских языков)<br>- Автоконтроль уровня громкость (AVL)<br>
					- Автовыключение питания<br>- Часы<br>- Игровой режим <br>- Режим \"Картинка-в-картинке\" (1 тюнер PIP)<br>
					Интерфейсы:<br>- HDMI<br>- USB <br>- Компонентный вход (Y/Pb/Pr)<br>
					- Компонентный вход (Y/Pb/Pr) 1 (для Component Y)<br>- Цифровой аудиовыход (оптический) x 1 (боковой)<br>
					- Вход для сигнала с ПК (D-sub)<br>- CI слот<br>- Scart<br>- Наушники<br>- РС Аудиовход (Mini Jack)<br>
					- DVI аудиовход (Mini Jack) x 1 ( для PC )					
				</p>
			");
			EshopportalElementAppend($catid, "ЖК-телевизор LG 22LK330", "tvlcd003-1", '', array('new'=>1, 'hit'=>1));
			EshopportalElementAppend($catid, "ЖК-телевизор Toshiba 32LV833RB", "tvlcd004-1");
			EshopportalElementAppend($catid, "ЖК-телевизор LG 32LD320B", "tvlcd005-1");
			EshopportalElementAppend($catid, "ЖК-телевизор Philips 56PFL9954H/12", "tvlcd006-1");
			EshopportalElementAppend($catid, "ЖК-телевизор Samsung LE-37A686M1F", "tvlcd007-1");
			EshopportalElementAppend($catid, "ЖК-телевизор Philips 47PFL4606H/60");
				

			$catid = EshopportalCatalogAppend($pcatid, 'tvplz', 'Плазменные телевизоры');
			EshopportalElementAppend($catid, "Плазменный телевизор Panasonic TX-PR42UT30");

			$catid = EshopportalCatalogAppend($pcatid, 'tvled', 'LED телевизоры');
			EshopportalElementAppend($catid, "LED телевизоры Toshiba 26EL833R");
				
			$catid = EshopportalCatalogAppend($pcatid, 'tvkinescope', 'Кинескопные телевизоры', "
				<p>
					Очень большие и тяжелые телевизоры прошлого века. К тому же потребляют
					существенное кол-во электроэнергии.
				</p>
			");
			EshopportalElementAppend($catid, "Кинескопный телевизор Supra CTV-14011");
				
			$catid = EshopportalCatalogAppend($pcatid, 'tvsat', 'Оборудование для спутникового и цифрового TV');
			EshopportalElementAppend($catid, "Комплект цифрового ТВ \"Vipr\" 6 месяцев");

			$catid = EshopportalCatalogAppend($pcatid, 'tvstend', 'Кронштейны и TV стенды');
			EshopportalElementAppend($catid, "TV стенд Holder LCDS - 5039");
				
			$catid = EshopportalCatalogAppend($pcatid, 'tvprop', 'Аксессуары для ТВ');
			EshopportalElementAppend($catid, "Philips SWV1431CN/10");
		
			// Крупная бытовая техника
			$pcatid = EshopportalCatalogAppend(0, 'bbtec', 'Крупная бытовая техника', "
				<p>
					Крупная бытовая техника делает нашу жизнь проще, экономя наше время, силы. 
					Современные нновационные технологии применяемые в бытовой техники сохраняет 
					окружающую среду и наше здоровье. 
				</p>
			");

			$catid = EshopportalCatalogAppend($pcatid, 'bbtecholod', 'Холодильники');
			$catid = EshopportalCatalogAppend($pcatid, 'bbtecmoroz', 'Морозильные камеры');
			$catid = EshopportalCatalogAppend($pcatid, 'bbtecstirka', 'Стиральные машины');
				

			// Компьютеры
			$pcatid = EshopportalCatalogAppend(0, 'pctec', 'Компьютерная техника');
			$catid = EshopportalCatalogAppend($pcatid, 'pctecpc', 'Компьютеры');
			$catid = EshopportalCatalogAppend($pcatid, 'pctecnout', 'Ноутбуки');
			$catid = EshopportalCatalogAppend($pcatid, 'pctecprint', 'Принтеры');
			$catid = EshopportalCatalogAppend($pcatid, 'pctecscan', 'Сканеры');
			$catid = EshopportalCatalogAppend($pcatid, 'pctecmonlcd', 'ЖК-мониторы');
				
			if ($devMode){
				// Режим разработчика
				// сюда можно включить специфичную инсталляцию 
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