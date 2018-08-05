<?php

/** $var array $config */
include 'main.php';

use Bixie\DfmApi\DfmApi;

$app = new Lime\App(array_merge($config['lime'], $config['dfm_api']));
$api = new DfmApi($config['dfm_api'], $app['debug']);

$app->helpers['apikey'] = 'Bixie\DfmApi\Helpers\ApiKeyHelper';
$app->helpers['requestparams'] = 'Bixie\DfmApi\Helpers\RequestParamsHelper';
$app->helpers['previewzip'] = 'Bixie\DfmApi\Helpers\PreviewZipHelper';

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

/**
 * Request preview image from dfm server
 * @param array $params Parameters for DFM
 * @param array $options Render options
 */
$app->post('/generate', function() use ($api) {
    //todo check csrf somehow
    $preview_id = uniqid('dfm_preview');
    $params = $this('requestparams')->getData('params');
    $options = $this('requestparams')->getData('options');
    $response = $api->post('/preview/' . $preview_id, compact('params', 'options'));
    if ($responseData = $response->getData()) {
        if ($responseData['result'] == true) {
            return ['preview_id' => $preview_id, 'result' => true,];
        } else {
            return ['preview_id' => $preview_id, 'result' => false, 'error' => $responseData['error']];
        }
    } else {
        return ['status' => 500, 'error' => $response->getError(),];
    }
});

/**
 * Request the preview image from server if available
 */
$app->get('/preview/:preview_id', function($params) {
    //todo check csrf somehow
    if (empty($params['preview_id'])) {
        return ['status' => 400, 'error' => 'No preview id!',];
    }
    $preview_id = (string)$params['preview_id'];
    $images = $this('previewzip')->getPreviewImagesContents($preview_id);
    if ($images === false) {
        return ['status' => 'pending', 'preview_id' => $preview_id,];
    }
//    $this('previewzip')->removeTempZip($preview_id);
    return ['preview_id' => $preview_id, 'status' => 'received', 'images' => $images,];
});

/**
 * Post the generated image to the server
 */
$app->post('/preview/:preview_id', function($params) {
    if (empty($_REQUEST['zipData'])) {
        return ['status' => 400, 'error' => 'No zip data!',];
    }
    if (!$this('apikey')->test()) {
        return ['status' => 401, 'error' => 'Invalid API key',];
    }
    $preview_id = (string)$params['preview_id'];
    $zipData = (string)$_REQUEST['zipData'];
    if (!$this('previewzip')->saveZipResponse($preview_id, $zipData)) {
        return ['status' => 500, 'error' => 'Error writing temp-zipfile',];
    }
    return ['preview_id' => $preview_id,];
});

/**
 * Error Handling
 */
$app->on('after', function() {

    switch($this->response->status){
        case '404':
            $this->response->body = ['error' => 'endpoint not found'];
            break;
        case '500':
            $this->response->body = ['error' => $this->response->body];
            break;
    }

    if (!empty($this->response->body['status']) && !empty($this->response->body['error'])) {
        $this->response->status = $this->response->body['status'];
        unset($this->response->body['status']);
    }
});

$app->run();

