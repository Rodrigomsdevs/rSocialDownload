<?php

header('Content-Type: application/json');

$msg = [];

try {
    $url = $_REQUEST['url'];

    if (empty($url)) {
        throw new Exception('Please provide the URL', 1);
    }

    $headers = [
        'sec-fetch-user: ?1',
        'sec-ch-ua-mobile: ?0',
        'sec-fetch-site: none',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'cache-control: max-age=0',
        'authority: www.facebook.com',
        'upgrade-insecure-requests: 1',
        'accept-language: en-GB,en;q=0.9,tr-TR;q=0.8,tr;q=0.7,en-US;q=0.6',
        'sec-ch-ua: "Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
    ];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // For Proxy uncomment the line below
    // curl_setopt($ch, CURLOPT_PROXY, 'http://<user>:<password>@<domain or IP address>:<port>');

    $data = curl_exec($ch);
    curl_close($ch);

    $msg['success'] = true;

    $msg['title'] = getTitle($data);
    $msg['description'] = getDescription($data);

    $msg['data'] = $data;
    $msg['links']['sd'] = extractAndDecodeURL($data, "browser_native_sd_url");
    $msg['links']['hd'] = extractAndDecodeURL($data, "browser_native_hd_url");
} catch (Exception $e) {
    $msg['success'] = false;
    $msg['message'] = $e->getMessage();
}

print_r($msg);
die();


function extractAndDecodeURL($inputString, $search)
{
    // Padrão regex para encontrar o URL codificado
    $pattern = '/"' . $search . '":"(.*?)"/';

    // Executa o regex na string de entrada
    if (preg_match($pattern, $inputString, $matches)) {
        // Decodifica o URL encontrado
        $decodedUrl = urldecode($matches[1]);

        // Substitui barras duplas por uma única barra, exceto após 'https:'
        $correctedUrl = preg_replace_callback(
            '#://|//#',
            function ($match) {
                // Retorna ':' para evitar substituição na parte do protocolo
                return ($match[0] == '://' ? '://' : '/');
            },
            $decodedUrl
        );

        $correctedUrl = str_replace("\/", "/", $correctedUrl);

        // Retorna o URL corrigido
        return $correctedUrl;
    } else {
        // Retorna falso se nenhum URL for encontrado
        return false;
    }
}


function getID($curl_content)
{

    $title = "";

    try {
        $title = explode('video_id":"', $curl_content)[1];
        $title = explode('"', $title)[0];
    } catch (\Throwable $th) {
        //throw $th;
    }

    return ($title);
}

function cleanStr($str)
{
    $tmpStr = "{\"text\": \"{$str}\"}";

    return json_decode($tmpStr)->text;
}

function removeTitleAttribute($inputString)
{
    // Padrão regex para encontrar e remover o atributo title
    // A expressão regular foi ajustada para remover apenas o atributo title, evitando efeitos colaterais
    $pattern = '/\stitle="[^"]*"/i'; // 'i' torna a busca insensível a maiúsculas e minúsculas

    // Remove o atributo title da string de entrada
    $cleanedString = preg_replace($pattern, '', $inputString);

    // Retorna a string sem o atributo title
    return $cleanedString;
}

function fixEncoding($string)
{
    // Converte caracteres Unicode escapados para a representação UTF-8 correspondente
    $fixed = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }, $string);

    // Tenta corrigir a dupla codificação UTF-8
    $fixed = utf8_decode($fixed); // Converte de UTF-8 para ISO-8859-1
    $fixed = utf8_encode($fixed); // Reconverte para UTF-8

    return $fixed;
}

function getTitle($curl_content)
{

    $title = "";

    try {
        $title = explode('message":{"text":"', $curl_content)[1];
        $title = explode('"', $title)[0];
    } catch (\Throwable $th) {
        //throw $th;
    }

    return fixEncoding($title);
}

function getDescription($curl_content)
{
    if (preg_match('/span class="hasCaption">(.+?)<\/span>/', $curl_content, $matches)) {
        return cleanStr($matches[1]);
    }

    return false;
}
