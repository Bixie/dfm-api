<?php

namespace Bixie\DfmApi;

use Bixie\DfmApi\Request\RequestHeaders;
use Bixie\DfmApi\Request\RequestParameters;
use Bixie\DfmApi\Request\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class DfmApi {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var CookieJar
	 */
	protected $cookieJar;

	/**
	 * @var string
	 */
	protected $apiKey;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var bool
	 */
	protected $debug;

	const HEADER_KEY_APITOKEN = 'x-dfm-apitoken';

	/**
	 * DevosClient constructor.
	 * @param      $config
	 * @param bool $debug
	 */
	public function __construct ($config, $debug = false) {
		$this->config = $config;
		$this->apiKey = $config['dfm.api_key'];

		$this->client = new Client(['base_uri' => $this->config['dfm.api_url']]);
		$this->debug = $debug;
	}

	/**
	 * @param string $url
	 * @param array $query
	 * @param array $headers
	 * @return Response Response from the service.
	 */
	public function get ($url, $query = [], $headers = []) {
		return $this->send('GET', $url, [], $query, $headers);
	}

	/**
	 * @param string $url
	 * @param array $data
	 * @param array $query
	 * @param array $headers
	 * @return Response Response from the service.
	 */
	public function post ($url, $data = [], $query = [], $headers = []) {
		return $this->send('POST', $url, $data, $query, $headers);
	}

	/**
	 * @param string $url
	 * @param array $data
	 * @param array $query
	 * @param array $headers
	 * @return Response Response from the service.
	 */
	public function delete ($url, $data = [], $query = [], $headers = []) {
		return $this->send('DELETE', $url, $data, $query, $headers);
	}

    /**
     * @param string $method
     * @param string $url
     * @param array  $data
     * @param array  $query
     * @param array  $headers
     * @return Response Response from the service.
     */
	public function send ($method, $url, $data = [], $query = [], $headers = []) {


		try {
			$response = $this->client->request($method, $url, [
				'query' => $query,
				'json' => $data,
				'headers' => $this->getHeaders($data, $query, $headers)->all(),
				'cookies' => $this->getCookies()
			]);

			return new Response($response);

		} catch (RequestException $e) {

			if ($e->hasResponse()) {
				return new Response($e->getResponse());
			}
			return new Response(new GuzzleResponse(500, [], null, ['reason_phrase' => $e->getMessage()]));

		} catch (GuzzleException $e) {

			return new Response(new GuzzleResponse(500, [], null, ['reason_phrase' => $e->getMessage()]));
		} catch (\Exception $e) {

			return new Response(new GuzzleResponse(500, [], null, ['reason_phrase' => $e->getMessage()]));
		}

	}

	/**
	 * @return bool|CookieJar
	 */
	protected function getCookies () {
		if (!isset($this->cookieJar) && $this->debug) {
			$this->cookieJar = CookieJar::fromArray([
				'XDEBUG_SESSION' => 'PHPSTORM'
			], (new Uri($this->config['dfm.api_url']))->getHost());
		}
		return $this->debug ? $this->cookieJar : false;
	}

	/**
	 * @param array             $headers
	 * @return RequestHeaders
	 */
	protected function getHeaders ($headers = []) {
		$headers['accept'] = 'application/json';
		$headers[DfmApi::HEADER_KEY_APITOKEN] = $this->apiKey;
		return new RequestHeaders($headers);
	}

}