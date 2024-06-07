<?php

class InstagramDownloader
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

        $title = $this->getTitle();
        $thumb = $this->getThumbnail();

        return [
            "url" => $this->videoUrl,
            "title" => $title,
            'thumb' => $thumb,
            'response' => $this->response,
        ];
    }


    public function getTitle()
    {
        $title = "";

        try {
            $title = explode("og:title\" content=\"", $this->response)[1];
            $title = explode('"', $title)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->fixEncoding($title);
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


    private function getThumbnail()
    {
        $thumbUrl = explode("og:image\" content=\"", $this->response)[1];
        $thumbUrl = explode('"', $thumbUrl)[0];
        $thumbUrl = str_replace("\/", "/", $thumbUrl);
        $thumbUrl = str_replace('&amp;', "&", $thumbUrl);

        return $thumbUrl;
    }
}
