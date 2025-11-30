<?


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Page\Asset;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<title><? $APPLICATION->ShowTitle(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?= SITE_TEMPLATE_PATH?>'/assets/images/favicon.604825ed.ico" type="image/x-icon">


	<?php

	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/common.css');
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/style.css');
	


	$APPLICATION->ShowHead(); ?>


</head>

<body>
	<div id="panel">
		<? $APPLICATION->ShowPanel(); ?>
	</div>
	