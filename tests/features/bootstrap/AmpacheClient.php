<?php

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Morris Jobke 2015
 * @copyright Pauli Järvinen 2017 - 2021
 */

use GuzzleHttp\Client;

/**
 * Abstraction for the specific Ampache API calls and how to parse the result
 */
class AmpacheClient {

	/** @var string the Ampache API URL */
	private $baseUrl;
	/** @var string auth token that is used in subsequent requests */
	private $authToken;

	/**
	 * connects to the API endpoint and authenticates the user
	 *
	 * @param string $baseUrl URL of the cloud instance
	 * @param string $userName
	 * @param string $password
	 * @throws AmpacheClientException if the authentication doesn't succeed
	 */
	public function __construct($baseUrl, $userName, $password) {
		$this->baseUrl = $baseUrl . '/apps/music/ampache/server/xml.server.php';

		$time = \time();
		$key = \hash('sha256', $password);
		$passphrase = \hash('sha256', $time . $key);

		$xml = $this->request('handshake', [
			'auth' => $passphrase,
			'timestamp' => $time,
			'version' => 350001,
			'user' => $userName,
		]);

		$authToken = $xml->xpath('/root/auth');
		if (!$authToken) {
			throw new AmpacheClientException('No auth token in response ' . $xml->asXML());
		}

		$this->authToken = $authToken[0]->__toString();
	}

	/**
	 * @return bool whether the auth token is present
	 */
	public function hasAuthToken() {
		return $this->authToken !== null;
	}

	/**
	 * requests the given Ampache method and returns an XML object
	 *
	 * @param $method
	 * @param array $options
	 * @return SimpleXMLElement
	 * @throws AmpacheClientException if the XML couldn't be parsed or there is an error in the response
	 * @throws Exception if the method isn't supported
	 */
	public function request($method, $options = []) {
		if (!\in_array($method, ['artists', 'handshake', 'albums', 'songs'])) {
			throw new Exception('Unsupported method: ' . $method);
		}

		$client = new Client(['verify' => false]);
		$response = $client->get($this->baseUrl, [
			'query' => \array_merge([
				'action' => $method,
				'auth' => $this->authToken,
			], $options)
		]);

		try {
			$xml = ClientUtil::getXml($response);
		} catch (Exception $e) {
			throw new AmpacheClientException('Could not parse XML', 0, $e);
		}

		$error = $xml->xpath('/root/error');

		if ($error) {
			throw new AmpacheClientException('Ampache error: ' . $error[0]->__toString() . ' (' . $error[0]->attributes()['code'] . ') ');
		}

		return $xml;
	}

}
