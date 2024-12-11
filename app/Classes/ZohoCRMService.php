<?php

namespace App\Classes;

use App\Classes\DTO\ZohoCRMAccountDTO;
use App\Classes\DTO\ZohoCRMDealDTO;
use App\Exceptions\ZohoCRMExceptions\ZohoCRMRequestFailedException;
use App\Exceptions\ZohoCRMExceptions\ZohoCRMRequestValidationFailedException;
use App\Exceptions\ZohoCRMExceptions\ZohoCRMServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZohoCRMService
{
    /** @var string */
    protected string $apiDomain;

    /** @var string */
    protected string $apiVersion = 'v7';

    /** @var string */
    protected string $domainSpecificAccountsUrl;

    /**
     * Used to refresh access token
     *
     * @var string
     */
    protected string $refreshToken;

    /**
     * Used to retrieve and store an access token
     *
     * @var string
     */
    protected string $accessTokenCacheKey = 'zoho_access_token';

    /**
     * Access token TTL described in docs
     *
     * @var int
     * @url https://www.zoho.com/crm/developer/docs/api/v7/token-validity.html
     */
    protected int $accessTokenDefaultTTL = 3600;

    public function __construct(
        protected string $accessToken = '',
    ) {
        $this->apiDomain = env('ZOHO_API_DOMAIN');
        $this->refreshToken = env('ZOHO_REFRESH_TOKEN');
        $this->domainSpecificAccountsUrl = env('ZOHO_DOMAIN_SPECIFIC_ACCOUNTS_URL');
    }

    /**
     * @param ZohoCRMAccountDTO $accountDTO
     * @return array
     * @throws ZohoCRMRequestFailedException
     * @throws ZohoCRMRequestValidationFailedException
     */
    public function createAccount(ZohoCRMAccountDTO $accountDTO): array
    {
        $url = $this->apiDomain . '/crm/' . $this->apiVersion . '/Accounts';

        $response = $this->makeRequest($url, $accountDTO->toArray());

        if ($response->successful()) {
            return $response->json()['data'];
        } else {
            throw new ZohoCRMRequestValidationFailedException($response->body(), $response->getStatusCode());
        }
    }

    /**
     * @param ZohoCRMDealDTO $dealDTO
     * @param string $accountId
     * @return array
     * @throws ZohoCRMRequestFailedException
     * @throws ZohoCRMRequestValidationFailedException
     */
    public function createDeal(ZohoCRMDealDTO $dealDTO, string $accountId): array
    {
        $url = $this->apiDomain . '/crm/' . $this->apiVersion . '/Deals';

        $data = $dealDTO->toArray();
        $data['Account_Name'] = [
            'id' => $accountId,
        ];

        $response = $this->makeRequest($url, $data);

        if ($response->successful()) {
            return $response->json()['data'];
        } else {
            throw new ZohoCRMRequestValidationFailedException($response->body(), $response->getStatusCode());
        }
    }

    /**
     * Refresh access token and put it into cache
     *
     * @return string
     * @throws ZohoCRMRequestFailedException
     */
    public function refreshAccessToken(): string
    {
        $url = $this->domainSpecificAccountsUrl . '/oauth/v2/token?' . http_build_query([
            'refresh_token' => $this->refreshToken,
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'grant_type' => 'refresh_token',
        ]);

        $result = Http::post($url);

        if ($result->successful()) {
            $result = $result->json();
            Cache::set($this->accessTokenCacheKey, $result['access_token'], $this->accessTokenDefaultTTL);

            return $result['access_token'];
        }

        throw new ZohoCRMRequestFailedException($result->body(), $result->getStatusCode());
    }

    /**
     * Retrieve an access token
     *
     * @return string
     * @throws ZohoCRMRequestFailedException
     */
    private function getAccessToken(): string
    {
        if (!$accessToken = Cache::get($this->accessTokenCacheKey)) {
            return $this->refreshAccessToken();
        }

        return $accessToken;
    }

    /**
     * Make a request to the API
     *
     * @param string $url
     * @param array $data
     * @return Response
     * @throws ZohoCRMRequestFailedException
     */
    private function makeRequest(string $url, array $data): Response
    {
        try {
            return Http::withToken($this->getAccessToken(), 'Zoho-oauthtoken')
                ->post($url, ['data' => [$data]]);
        } catch (ConnectionException $e) {
            throw new ZohoCRMRequestFailedException($e);
        }
    }
}
