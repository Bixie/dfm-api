<?php

/** $var array $config */
include 'main.php';

use Bixie\DfmApi\DfmApi;

$app = new Lime\App(array_merge($config['lime'], $config['dfm_api']));
$api = new DfmApi($config['dfm_api'], $app['debug']);

$app->helpers['apikey'] = 'Bixie\DfmApi\Helpers\ApiKeyHelper';
$app->helpers['previewimage'] = 'Bixie\DfmApi\Helpers\PreviewImageHelper';

$app->bind('/', function() {
    return 'API client/server for DFM preview requests';
});

//session_set_cookie_params(36000, '/', $app['session.cookie_domain']);
//$app('session')->init();
//
//$csrf = $app('session')->read('dfm.csrf');
//if (!$csrf) {
//    $app('session')->write('dfm.csrf', 'sdfghkhjklhh;h;;;hsdfsgshhs');
//}

$app->post('/preview/:preview_id', function($params) {
    if (empty($_REQUEST['imageData'])) {
        return ['status' => 400, 'message' => 'No image data!',];
    }
    if (!$this('apikey')->test()) {
        return ['status' => 401, 'message' => 'Invalid API key',];
    }
    $preview_id = (string)$params['preview_id'];
    $imageData = (string)$_REQUEST['imageData'];
    if (!$this('previewimage')->saveTempImage($preview_id, $imageData)) {
        return ['status' => 500, 'message' => 'Error writing temp-file',];
    }
    return ['preview_id' => $preview_id];
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