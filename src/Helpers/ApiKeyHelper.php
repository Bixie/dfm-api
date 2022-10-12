<?php


namespace Bixie\DfmApi\Helpers;


use Bixie\DfmApi\Lime\Helper;

class ApiKeyHelper extends Helper {

    const HEADER_KEY_APITOKEN = 'HTTP_X_DFM_APITOKEN';

    /**
     * Test if a valid API key was provided
     * @return bool
     */
    public function test() {
        $api_key = $_SERVER[self::HEADER_KEY_APITOKEN] ?? '';
        return $api_key === $this->app['api_key'];
    }
}
