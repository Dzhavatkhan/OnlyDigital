<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Agents\Iblock;
use Bitrix\Main\Loader;


$APPLICATION->SetTitle("тест");

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/DevSite/lib/Agents/Iblock.php');

?>

<?php

$agent = new Iblock();

$result = $agent->GetBlock();

?>
<pre><? print_r($result); ?></pre>
<?


?>