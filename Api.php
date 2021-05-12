<?php

use GuzzleHttp\Client;

/**
 *
 */
class Api
{
    protected $config;

    protected $client;

    protected $token;

    public function __construct()
    {
        $this->config = include('config.php');
        $this->db = include('db.php');
        $this->client = new Client();
        $this->token  = $this->config['token'];
    }

    public function get($data, $apiName)
    {
        $response = $this->client->request(
            'GET',
            $this->config['api_url'] . $this->config[$apiName],
            [
                'json'    => $data,
                'headers' => $this->config['headers']
            ]
        );

        $body = json_decode((string) $response->getBody(), true);
        if ($body['status'] === 'success') {
            return $body['response'][$apiName];
        }
        else {
            throw new \Exception($body['exception']);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getDb()
    {
        return $this->db;
    }
}
