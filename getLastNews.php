<?php
/**
 * Created by PhpStorm.
 * User: Shand
 * Date: 4/12/2019
 * Time: 1:15 PM
 */

header('Content-type: text/plain; charset=utf-8');
$allowOnlyConsole = true;
$runMethod = php_sapi_name();
$newsCount = 5;

try {
    if ($runMethod != 'cli' && $allowOnlyConsole === true) {
        throw new Exception("Running allowed only in console mode.");
    }

} catch (Exception $e) {
    exit($e->getMessage());
}
try {
    $url = 'https://lenta.ru/rss';
    $content = file_get_contents($url);
    $rssItem = new SimpleXMLElement($content);

    if ($rssItem && isset($rssItem->channel->item)) {
        $index = 1;

        foreach ($rssItem->channel->item as $item) {
            echo trim($item->title) . PHP_EOL;
            echo trim($item->link) . PHP_EOL;
            echo trim($item->description) . PHP_EOL . PHP_EOL;

            if ($index >= $newsCount) {
                break;
            }

            $index++;
        }
    } else {
        echo "RSS is empty or not found";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>