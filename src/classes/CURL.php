<?php

class CURL
{

    private $url;
    private $response;

    public function __construct()
    {
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getResponse()
    {
        $this->executeResponse();
        return $this->response;
    }

    private function executeResponse()
    {

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
        curl_setopt($ch, CURLOPT_URL, $this->url);
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


        $this->response = $data;

        return $data;
    }
}
