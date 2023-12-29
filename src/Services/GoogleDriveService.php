<?php

use Google\Service\Drive\Drive;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GoogleDriveService extends AbstractController
{
    private $client;
    private $driveService;

    function __construct(string $credentialsPath)
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->getParameter('googleClientId'));
        $this->client->setClientSecret($this->getParameter('googleClientSecret'));
        $this->client->setScopes('https://www.googleapis.com/auth/drive');
        $this->client->setAccessType('offline');

        $this->driveService = new Drive($this->client);
        $refreshToken = "Your refresh token";
        $tokens = file_get_contents('token.json');
        $this->client->setAccessToken($tokens);

        if ($this->client->isAccessTokenExpired()) {
            $this->client->refreshToken($refreshToken);
            file_put_contents('token.json', $this->client->getAccessToken());
        }
    }
}
