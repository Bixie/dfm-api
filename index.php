<?php

/** $var array $config */
include 'main.php';

use Bixie\DfmApi\DfmApi;

$app = new Lime\App(array_merge($config['lime'], $config['dfm_api']));
$api = new DfmApi($config['dfm_api'], $app['debug']);

$app->helpers['apikey'] = 'Bixie\DfmApi\Helpers\ApiKeyHelper';
$app->helpers['requestparams'] = 'Bixie\DfmApi\Helpers\RequestParamsHelper';
$app->helpers['previewzip'] = 'Bixie\DfmApi\Helpers\PreviewZipHelper';
$app->helpers['drinput'] = 'Bixie\DfmApi\Helpers\DRInputHelper';
$app->helpers['keygenauth'] = 'Bixie\DfmApi\Helpers\KeygenAuthenticator';
$app->helpers['keygenerator'] = 'Bixie\DfmApi\Helpers\KeyGenerator';
$app->helpers['logger'] = 'Bixie\DfmApi\Helpers\Logger';

//try get name from cookie
//$app('session')->init($sessionname=null);

$app->bind('/', function() {
    return 'API client/server for DFM preview requests';
});

/**
 * Request preview image from dfm server
 * @param array $params Parameters for DFM
 * @param array $options Render options
 */
$app->post('/request', function() use ($api) {
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
    $files = $this('previewzip')->getPreviewFilesContents($preview_id);
    if ($files === false) {
        return ['preview_status' => 'pending', 'preview_id' => $preview_id,];
    }
//    $this('previewzip')->removeTempZip($preview_id);
    return ['preview_id' => $preview_id, 'preview_status' => 'received', 'files' => $files,];
});

/**
 * Post the generated image to the server
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
 * Request license key for DFM, requested by DR
 * https://account.mycommerce.com/home/wiki/Web%20Key%20Generators
 * https://account.mycommerce.com/home/wiki/Input%20Values
 */
$app->post('/keygen', function() use ($api) {
    $this->response->mime = 'asc'; //DR expects plain text
    if (!$this('keygenauth')->authenticate()) {
        $this->response->status = 401;
        return 'Unauthenticated';
    }
    $data = $this('drinput')->getData();
    try {
        $key = $this('keygenerator')->generateKeyFromId($data['PURCHASE_ID']);
        $this('logger')->info(
            sprintf('License key for order %s, email %s: %s', $data['PURCHASE_ID'], $data['EMAIL'], $key)
        );
        //not interested in response
        $api->post('/license', compact('data', 'key'));
        return $key;
    } catch (Exception $e) {
        $this('logger')->error(
            sprintf('Error creating license key for order %s, email %s', $data['PURCHASE_ID'] ?? '', $data['EMAIL'] ?? '')
        );
        $this->response->status = 500;
        return $e->getMessage();
    }
});

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
            //todo log
            break;
    }

    if (is_array($this->response->body) && !empty($this->response->body['status']) && !empty($this->response->body['error'])) {
        $this->response->status = $this->response->body['status'];
        unset($this->response->body['status']);
    }
});

$app->run();

