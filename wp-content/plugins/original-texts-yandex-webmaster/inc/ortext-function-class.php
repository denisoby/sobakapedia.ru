<?php

/**
 * Класс с функционалом и обработками
 */
class OrTextFunc {

    const YANDEX_ADD_APLICATION = 'https://oauth.yandex.ru/client/new'; //Урл создания нового приложения Яндекс
    const YANDEX_APP_APLICATION = 'https://oauth.yandex.ru/client/my'; // Урл к выбору прилжоения
    const YANDEX_Callback_FUNCTION = 'https://oauth.yandex.ru/verification_code'; // УРЛ Функции callback для приложения
    const YANDEX_TOKEN_URL = 'https://oauth.yandex.ru/authorize?response_type=code&client_id='; //УРЛ для получения токена
    const YANDEX_WEBMASTER_HOST = 'webmaster.yandex.ru'; // УРЛ ВебМастера
    const YANDEX_API_REQUEST_TIMEOUT = 5; // Таймаунт запроса
    const YANDEX_MAX_POST_DAY = '100'; //Максимальное количество текстов в сутки
    const YANDEX_MIN_SIZE_POST = '500'; // Минимальное количество символов  в посте
    const YANDEX_MAX_SIZE_POST = '32000'; // Максимальное количество символов в посте

    /**
     * Условия
     */

    public function IfElseUpdate() {
        $ortext_posttype = get_option('ortext_posttype'); //Типы постов
        if (empty($ortext_posttype)) { //установка опции по умолчанию
            update_option('ortext_posttype', array('post' => 'post'));
        }
    }

    /**
     * Получение от Яндекса Токена
     * 
     * @return string
     */
    public function getYandexToken() {
        $ortext_id = get_option('ortext_id');
        $url = self::YANDEX_TOKEN_URL . $ortext_id;
        return $url;
    }

    /**
     * Отправка зпроса Yandex, возврат список сайтов
     */
    public function getWebsiteXml() {

        $ortext_id = get_option('ortext_id');
        $ortext_passwd = get_option('ortext_passwd');
        $ortext_token = get_option('ortext_token');
        $ortext_token_key = get_option('ortext_token_key'); // Токен яндекса

        $headers = array(
            'GET /api/v2/hosts HTTP/1.1',
            'Host: webmaster.yandex.ru',
            'Authorization: OAuth ' . $ortext_token_key
        );

        $requestOptions = array(
            CURLOPT_URL => 'https://' . self::YANDEX_WEBMASTER_HOST . '/api/v2/hosts',
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CONNECTTIMEOUT => self::YANDEX_API_REQUEST_TIMEOUT,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        );

        $response = $this->getPage($requestOptions);
        if ($response['info']['http_code'] == 200 and strlen($response['result']) > 0) {
            $dom = new DOMDocument();
            if ($dom->loadXML($response['result'])) {
                foreach ($dom->getElementsByTagName('host') as $host) {
                    $host_href = $host->getAttribute('href');
                    //echo $host_href."<br>";
                    $id_array=explode("/", $host_href);
                    $siteid = array_pop($id_array); //id сайта
                    //echo $siteid."<br>";
                    $name = $host->getElementsByTagName('name')->item(0)->nodeValue;
                    //print_r($name);
                    $status = $host->getElementsByTagName('verification')->item(0)->getAttribute('state');
                    $result[] = array('name' => $name, 'status' => $status, 'siteid' => $siteid);
                }
            }

            // return $response;
            return $result;
        }
        return "Ошибка";
    }

    /**
     * Запрос токена у яндекса + время жизни токена
     * @return object     $result->access_token - Токен
     * $result->expires_in - Время жизни токена в секундах
     * 
     */
    public function zaprosToken() {
        $url = 'https://oauth.yandex.ru/token';
        $ortext_id = get_option('ortext_id');
        $ortext_passwd = get_option('ortext_passwd');
        $ortext_token = get_option('ortext_token');
        $postData = "grant_type=authorization_code&code=" . $ortext_token . "&client_id=" . $ortext_id .
                "&client_secret=" . $ortext_passwd;
        $headers = array(
            "POST /token HTTP/1.1",
            "Host: oauth.yandex.ru",
            "Content-type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($postData)
        );
        $curlOptions = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => self::YANDEX_API_REQUEST_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $headers
        );
        $response = $this->getPage($curlOptions);
        //print_r($response);
        if ($response['info']['http_code'] == 200) {
            return json_decode($response['result']);
        }
        return "Не получилось";
    }

    /**
     * Запрос параметров через Курл
     * @param type $curlOptions
     * @return type
     */
    public function getPage($curlOptions = array()) {
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        return array('result' => $result, 'info' => $info);
    }

    /**
     * Отправка Текстов в Сервис Оригинальные тексты
     * @param string $text2 Текст для загрузки
     */
    public function sendTextOriginal2($text2) {
        
        $ortext_loadsite = get_option('ortext_loadsite'); //Текущий загруженный проект
        $ortext_token_key = get_option('ortext_token_key'); // Токен яндекса
        $url = '/api/v2/hosts/' . $ortext_loadsite . '/original-texts/';
        $text = urlencode('<original-text><content>' . $text2 . '</content></original-text>');

        $headersnew = array(
            'POST' => $url . ' HTTP/1.1',
            'Host' => 'webmaster.yandex.ru',
            'Authorization' => 'OAuth ' . $ortext_token_key,
            'Content-Length' => strlen($text)
        );

        $curlinfo = wp_remote_post('https://' . self::YANDEX_WEBMASTER_HOST . $url, array('headers' => $headersnew, 'body' => $text));
       if (is_wp_error($curlinfo)) { //Проверка переменной на содержание ошибки
            //$this->logJornal('000', 'function sendTextOriginal2 ', 'Ошибка обращения к Yandex', 'pluginError');
            return 000;
       } else {
            $response = $curlinfo['response'];
            switch ($response['code']) {
                case 201: return 201;

                case 400: return 400;

                case 403: return 403;

                case 401: return 401;

                case 409: return 409;

                case 500: return 500;

                default: return 777;
            }
        }
    }

    /**
     * Проверка Чекеда
     * @param string $options Опция из базы данных
     * @param string $value Текущее значение для сравнения (например значение из цикла)
     * @return echo checked или пусто
     */
    public function chekedOptions($options, $value) {
        if (!empty($options) or ! empty($value)) {
            if ($options == $value) {
                echo 'checked';
            } elseif ($options !== $value) {
                echo '';
            }
        }
    }

    /**
     * Функция логирования, для вкладки журнал
     * @param int $idpost ид поста
     * @param string $title Заголовок поста
     * @param string $status Статуст поста
     * @param string $post_type Тип поста
     */
    public function logJornal($idpost, $title, $status, $post_type) {
        $includ_jornal = get_option('ortext_jornal_inc');
        if ($includ_jornal == '1') {

            $ortext_jornal_old = get_option('ortext_jornal');
            //$time=date('d-m-Y', time()); // Дата запуска функции
            $time = current_time('mysql');
            $ortext_jornal_temp = array('time' => $time, 'idpost' => $idpost, 'title' => $title, 'status' => $status, 'post_type' => $post_type);
            $ortext_jornal_new = array();
            $ortext_jornal_new = $ortext_jornal_old;
            array_push($ortext_jornal_new, $ortext_jornal_temp);
            update_option('ortext_jornal', $ortext_jornal_new);
        }
    }

}
