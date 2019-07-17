<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use function GuzzleHttp\json_decode;

class InfoClient
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    private $rpcClient;
    public function __construct(ContainerInterface $container, MockClient $mockClient)
    {
        $this->client = $container->get('eight_points_guzzle.client.info');
        $this->rpcClient = $mockClient;
    }

    public function call(string $path, array $arr) : array
    {
        $mode = $_SERVER['SVC_MODE'] ?? 'api';
        if ('rpc' === $mode) {
            return (array) ($this->rpcClient->request('info', $path, $arr));
        }
        try {
            $res = $this->client->post($path, ['json' => $arr]);
            $r = json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $r;
    }
}
