<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/DevSite/lib/Agents/Iblock.php');

echo "<h2>Проверка агентов</h2>";

$agent = \CAgent::GetList(
    ["ID" => "DESC"],
    ["%NAME" => "CleanLog"]
);

echo "<h3>Найденные агенты:</h3>";
$agentsFound = false;
while ($a = $agent->Fetch()) {
    $agentsFound = true;
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}

if (!$agentsFound) {
    echo "<p style='color: red;'>Агенты не найдены!</p>";
}

echo "<h3>Ручной запуск агента:</h3>";
try {
    $result = \Agents\Iblock::CleanLog();
    echo "Результат: " . htmlspecialchars($result) . "<br>";
    
    $agent = \CAgent::GetList(
        [],
        ['NAME' => 'Agents\\Iblock::CleanLog();']
    )->Fetch();
    
    if ($agent) {
        $nextExec = ConvertTimeStamp(time() + $agent['AGENT_INTERVAL'], 'FULL');
        \CAgent::Update($agent['ID'], [
            'LAST_EXEC' => ConvertTimeStamp(time(), 'FULL'),
            'NEXT_EXEC' => $nextExec
        ]);
        echo "<p style='color: green;'>Время обновлено. Следующий запуск: $nextExec</p>";
    } else {
        echo "<p style='color: orange;'>Агент не найден в базе после запуска</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}

echo "<h3>Последние события из лога:</h3>";
$eventLog = \CEventLog::GetList(
    ["ID" => "DESC"],
    ["AUDIT_TYPE_ID" => "%AGENT%"],
    false,
    ["nTopCount" => 10]
);

$eventsFound = false;
while ($event = $eventLog->Fetch()) {
    $eventsFound = true;
    echo date('Y-m-d H:i:s', MakeTimeStamp($event['TIMESTAMP_X'])) . " - " 
         . $event['AUDIT_TYPE_ID'] . ": " . $event['DESCRIPTION'] . "<br>";
}

if (!$eventsFound) {
    echo "<p style='color: orange;'>События не найдены</p>";
}

echo "<h3>Проверка инфоблока LOG:</h3>";
$iblockData = \Agents\Iblock::GetBlock();
if ($iblockData) {
    echo "<p style='color: green;'>Инфоблок LOG найден:</p>";
    echo "<pre>";
    print_r($iblockData);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Инфоблок LOG не найден!</p>";
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");