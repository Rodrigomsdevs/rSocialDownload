<?php

class FacebookDownloader
{

    private $curl;
    private $videoUrl;
    private $response;

    public function __construct($videoUrl)
    {
        require("CURL.php");
        $this->curl = new CURL();
        $this->videoUrl = $videoUrl;
    }

    public function execute()
    {
        $this->curl->setUrl($this->videoUrl);
        $this->response = $this->curl->getResponse();
    }

    public function getResponse()
    {

        $browser_native_hd_url = $this->extractAndDecodeURL("browser_native_hd_url");
        $browser_native_sd_url = $this->extractAndDecodeURL("browser_native_sd_url");


        if (empty($browser_native_hd_url) && empty($browser_native_sd_url)) {
            $msg['success'] = false;
            $msg['msg'] = "Nao foi possivel obter o video !";
            return $msg;
        }


        $msg['success'] = true;
        $msg['infos'] = array(
            'platformName' => 'Facebook'
        );
        $msg['videoUrl'] = $this->videoUrl;
        $msg['title'] = $this->getTitle();
        $msg['links']['sd'] = $browser_native_sd_url;
        $msg['links']['hd'] = $browser_native_hd_url;
        $msg['thumbnail'] = $this->getThumbnail();

        $msg['response'] = $this->response;

        return $msg;
    }

    private function getThumbnail()
    {
        $thumbUrl = explode('thumbnail":{"image":{"uri":"', $this->response)[1];
        $thumbUrl = explode('"', $thumbUrl)[0];
        $thumbUrl = str_replace("\/", "/", $thumbUrl);
        return $thumbUrl;
    }


    public function extractAndDecodeURL($search)
    {
        // Padrão regex para encontrar o URL codificado
        $pattern = '/"' . $search . '":"(.*?)"/';

        // Executa o regex na string de entrada
        if (preg_match($pattern, $this->response, $matches)) {
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


    public function getID($curl_content)
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

    public function removeTitleAttribute($inputString)
    {
        // Padrão regex para encontrar e remover o atributo title
        // A expressão regular foi ajustada para remover apenas o atributo title, evitando efeitos colaterais
        $pattern = '/\stitle="[^"]*"/i'; // 'i' torna a busca insensível a maiúsculas e minúsculas

        // Remove o atributo title da string de entrada
        $cleanedString = preg_replace($pattern, '', $inputString);

        // Retorna a string sem o atributo title
        return $cleanedString;
    }

    public  function fixEncoding($string)
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

    public function getTitle()
    {

        $title = "";

        try {
            $title = explode('message":{"text":"', $this->response)[1];
            $title = explode('"', $title)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->fixEncoding($title);
    }
}
