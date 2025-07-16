<?php

namespace App\Models;

use Nette;
use Nette\Http\Client;
use Nette\Http\RequestFactory;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class HeliosAPIModel extends BaseModel {

	private $baseUrl = 'http://localhost:8888/api';
	private $httpClient;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function getAccessToken(string $username, string $password, string $clientId, string $clientSecret): string
	{
		$url = $this->baseUrl . '/token';

		$response = $this->httpClient->post($url, [
			'grant_type' => 'password',
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'username' => $username,
			'password' => $password,
		]);

		try {
			$data = Json::decode($response->getBody());
			return $data->access_token;// ?? throw new \RuntimeException('Access token nebyl vrácen.');
		} catch (JsonException $e) {
			//throw new \RuntimeException('Chybná odpověď při přihlášení: ' . $e->getMessage());
		}
	}

	public function printFormPreset(
		string $accessToken,
		string $dbName,
		string $browseIdent,
		array $recordIds,
		string $printFormId,
		string $printDefId,
		string $printType = 'inBulk',
		bool $fileContent = true
	): string {
		$recordIdsPath = implode(',', $recordIds);

		$url = sprintf(
			'%s/v1/browses/print/%s/%s/%s/%s/%s',
			$this->baseUrl,
			rawurlencode($dbName),
			rawurlencode($browseIdent),
			rawurlencode($recordIdsPath),
			rawurlencode($printFormId),
			rawurlencode($printDefId)
		);

		$response = $this->httpClient->get($url, [
			'headers' => [
				'Authorization' => 'Bearer ' . $accessToken,
			],
		]);

		return $response->getBody(); // binární obsah (např. PDF)
	}

}