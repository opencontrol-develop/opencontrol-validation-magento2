<?php

namespace OpenControl\Integration\Model\Http;

use OpenControl\Integration\Model\Http\AuthDto;

class HttpClient 
{
    /**
     * These constant values ​​were taken from the official OpenControl documentation
     */
    const MAX_REDIRECTS = 10;
    const TIMEOUT = 30;

    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    
    public function execute($url, $data, AuthDto $auth = null, $method = 'GET') {
        //$this->logger->debug('#HttpClient', ['url'=>$url, 'data'=>$data]);
        $curl = curl_init();

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        if($auth !== null){
            $headers[] = 'Authorization: Basic '.$auth->toBase64();
        }

        if($data !== null && $method === "POST"){
            $postfields = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        }

        curl_setopt_array($curl , [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = json_decode(curl_exec($curl), true);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $httpResponse = new HttpResponse();
        $httpResponse->httpCode = $httpCode;
        $httpResponse->body = $response;

        $this->logger->debug("#responseHttp", ["response"=>json_encode($httpResponse)]);

        return $httpResponse;
    }
}