<?php

namespace App;

class HttpClient
{
    private $response;

    public function get(string $url): void
    {
        //TODO: validate $url
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        try {
            $result = curl_exec($curl);

            //TODO: implement response object or use external dependency
            $this->response = new \stdClass();

            if (curl_errno($curl)) {
                echo 'Curl error: ' . curl_error($curl);
                $this->response->statusCode = 404;
                $this->response->content = $result;
                return;
            }

            $this->response->statusCode = 200;
            $this->response->content = $result;
        } catch (\Exception $e) {
            $this->response->statusCode = 500;
            $this->response->content = $e->getMessage();
        } finally {
            curl_close($curl);
        }
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }
}
