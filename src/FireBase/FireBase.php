<?php

    namespace Wixnit\DeepInsight\FireBase;
    

    use Exception;
    use GuzzleHttp\Client;
    use Google\Auth\ApplicationDefaultCredentials;

    class FireBase
    {
        protected string $projectId;
        protected string $token;

        function __construct(string $accessToken, string $projectId)
        {
            $this->token = $accessToken;
            $this->projectId = $projectId;
        }

        public function SendNotification(Message $message)
        {
            $client = new Client();

            try{
                $response = $client->request('POST', 'https://fcm.googleapis.com/v1/projects/'.$this->projectId.'/messages:send', [
                    'headers' => [
                        'Authorization' => "Bearer ".$this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'message'=> $message->toObject(),
                    ]
                ]);
                $result = json_decode($response->getBody());

                if(($response->getStatusCode() != 200) && ($response->getStatusCode() != 201))
                {
                    throw(new Exception("Authorization failed"));
                }
            }
            catch(Exception $e)
            {
                //dont do anything for now
                throw(new Exception($e->getMessage()));
            }
        }

        public static function GetAccessToken(): string
        {
            $scope = 'https://www.googleapis.com/auth/firebase.messaging';
            $credentials = ApplicationDefaultCredentials::GetCredentials($scope);
            $tempauxarrayresult = $credentials->fetchAuthToken();

            return $tempauxarrayresult['access_token'];
        }
    }