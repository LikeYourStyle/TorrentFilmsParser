<?php
include ('simple_html_dom.php');
include ('connection.php');
include ('url_to_absolute/url_to_absolute.php');
define('BASE_URL',
    'site.html' // for debug
    //'http://filmitorrent.net/'
);

$last_films_count = 0;
$list_info = [
    'a'=>[],
    'rate'=>[]
];

$link = mysqli_connect($host,$user,$password,$database) or die("Ошибка при соединении ".mysqli_error($link));

$html = file_get_html(BASE_URL);

$list_info['a'] = $html->find('div[class=post-title] a');
$list_info['rate'] = $html->find("div[class=cell2] img");

//include_once "bot_functions/sendMessage.php";

try {
    foreach ($list_info['a'] as $item) {
        $query = mysqli_query($link, "SELECT EXISTS(select null from films t where t.film = '" . trim($item->plaintext) . "') film_exist");
        $result = $query->fetch_assoc();
        var_dump($result);
        return;
        if (!$result) {
            $film_name = $item->plaintext;
            $film_link = trim($item->href);
            $film_rate = url_to_absolute(BASE_URL, $item->src);
            //notifyBot($film_name, $film_link, $film_rate);
            $query = "INSERT INTO films (film, link, rate_uri) VALUES ('$film_name', '$film_link', '$film_rate')";
            if (!mysqli_query($link, $query)) {
                notifyBot($exception->getMessage(), 'Error', 'Error');
            }
            echo $film_name;
        }
    }
} catch (Exception $exception){
    notifyBot($exception->getMessage(), 'Error', 'Error');
}

mysqli_close($link);
$html->clear();
unset($html);
?>