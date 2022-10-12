<?php

/** @var array $config */
include 'main.php';

use Bixie\DfmApi\DfmApi;
use Bixie\DfmApi\Lime\App;

$app = new App(array_merge($config['lime'], $config['dfm_api']));
//not used in endpoints for the moment. Module helpers DO use this class
$api = new DfmApi($config['dfm_api'], $app['debug']);

$app->helpers['apikey'] = 'Bixie\DfmApi\Helpers\ApiKeyHelper';
$app->helpers['previewzip'] = 'Bixie\DfmApi\Helpers\PreviewZipHelper';
$app->helpers['logger'] = 'Bixie\DfmApi\Helpers\Logger';

$app->bind('/', function() {
    return 'API client/server for DFM preview requests';
});

/**
 * Receive the generated image from the server
 */
if ($app->req_is('put')) { //no shorthand function for put
    $app->bind('/preview/:preview_id', function($params) {
        if (!$this('apikey')->test()) {
            return ['status' => 401, 'error' => 'Invalid API key',];
        }
        if (empty($params['preview_id'])) {
            return ['status' => 400, 'error' => 'No preview id!',];
        }
        $preview_id = (string)$params['preview_id'];
        if (!$this('previewzip')->saveZipResponse($preview_id)) {
            return ['status' => 500, 'error' => 'Error writing temp-zipfile',];
        }
        return ['preview_id' => $preview_id,];
    });
}

/**
 * Error Handling
 */
$app->on('after', function() {

    switch($this->response->status){
        case '404':
            $this->response->body = ['error' => 'Endpoint not found'];
            break;
        case '500':
            $this->response->body = ['error' => $this->response->body];
            $this('logger')->error($this->response->body);
            break;
    }

    if (is_array($this->response->body) && !empty($this->response->body['status']) && !empty($this->response->body['error'])) {
        $this->response->status = $this->response->body['status'];
        unset($this->response->body['status']);
    }
});

$app->run();

