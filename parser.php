<?php
include ('simplehtmldom_1_5/simple_html_dom.php');
include ('connection.php');
include ('url_to_absolute/url_to_absolute.php');
define('BASE_URL',
    //'site.html' // for debug
    'http://filmitorrent.xyz/'
);

$last_films_count = 0;
$list_info = [
    'a'=>[],
    'rate'=>[]
];

$link = mysqli_connect($host,$user,$password,$database);

$html = file_get_html(BASE_URL);

$list_info['a'] = $html->find('div[class=post-title] a');
$list_info['rate'] = $html->find("div[class=cell2] img");

include_once "bot_functions/sendMessage.php";

try {
    foreach ($list_info['a'] as $item) {
        $result = mysqli_query($link, "SELECT 1 as res from films t where t.film = '" . trim($item->plaintext) . "'");
        notifyBot("SELECT 1 from films t where t.film = '" . trim($item->plaintext) . "'", 'test', 'test');
        $result->fetch_assoc();
        if (!$result) {
            $film_name = $item->plaintext;
            $film_link = trim($item->href);
            $film_rate = url_to_absolute(BASE_URL, $item->src);
            notifyBot($film_name, $film_link, $film_rate);
            $query = "INSERT INTO films (film, link, rate_uri) VALUES ('$film_name', '$film_link', '$film_rate')";
            mysqli_query($link, $query);
        } else {
            notifyBot($result['res'], 'test', 'test');
        }
    }
} catch (Exception $exception){
    notifyBot($exception->getMessage(), 'test', 'test');
}
$m_query = "DELETE FROM films;";
$m_query .= "ALTER TABLE films AUTO_INCREMENT = 1";

mysqli_multi_query($link, $m_query);

$link->next_result();
$link->store_result();

mysqli_close($link);
$html->clear();
unset($html);
?>