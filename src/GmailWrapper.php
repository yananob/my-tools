<?php

declare(strict_types=1);

namespace yananob\MyTools;

// use Google\Client;
use Google\Service\Gmail;

class GmailWrapper
{
    /**
     * Returns an authorized API client.
     * @return \Google\Client the authorized client object
     */
    public static function getClient(array $authConfig, array $apitoken_data)
    {
        $logger = new Logger("GoogleAPI");

        $SCOPES = [
            Gmail::MAIL_GOOGLE_COM,
            Gmail::GMAIL_MODIFY,
            Gmail::GMAIL_READONLY,
        ];
        $client = new \Google\Client();
        $client->setApplicationName('MyCFApp');
        $client->setScopes($SCOPES);
        $client->setAuthConfig($authConfig);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        // $client->setRedirectUri('https://www.google.com/');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        // https://sqripts.com/2022/08/25/20386/
        $client->setAccessToken($apitoken_data);

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            $logger->log("AccessToken expired");
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $logger->log("fetching Access token with refresh token");
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // ★THIS DOESNT WORK. use hello_gmail/create_refresh_token instead
                throw new \Exception("THIS DOESN'T WORK");
            }
        }
        return $client;
    }
}
