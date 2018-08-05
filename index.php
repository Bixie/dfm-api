<?php

/** $var array $config */
include 'main.php';

use Bixie\DfmApi\DfmApi;

$app = new Lime\App(array_merge($config['lime'], $config['dfm_api']));
$api = new DfmApi($config['dfm_api'], $app['debug']);

$app->helpers['apikey'] = 'Bixie\DfmApi\ApiKeyHelper';

$app->bind('/', function() {
    return 'API client/server for DFM preview requests';
});

$app->post('/preview/:preview_id', function($params) {
    if (empty($_REQUEST['imageData'])) {
        return ['status' => 400, 'message' => 'No image data!',];
    }
    if (!$this('apikey')->test()) {
        return ['status' => 401, 'message' => 'Invalid API key',];
    }
    $imageData = (string)$_REQUEST['imageData'];
    return ['preview_id' => $params['preview_id']];
});

$app->on('after', function() {

    switch($this->response->status){
        case '404':
            $this->response->body = ['message' => 'endpoint not found'];
            break;
        case '500':
            $this->response->body = ['message' => $this->response->body];
            break;
    }

    if (!empty($this->response->body['status']) && !empty($this->response->body['message'])) {
        $this->response->status = $this->response->body['status'];
        unset($this->response->body['status']);
    }
});

$app->run();

//
//$response = $client->get('/api/shipment', compact('filter'));
//
//if ($responseData = $response->getData()) {
//	echo '<pre>';
//	echo print_r($responseData);
//	echo '</pre>';
//
//} else {
//	echo $response->getError();
//}