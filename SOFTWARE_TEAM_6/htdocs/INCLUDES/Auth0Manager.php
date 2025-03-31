<?php
class Auth0Manager
{
    private $client_id     = 'm2pLRjG1FbG3JjA2Zv8xnzlLfqblk68s';
    private $client_secret = 'O1IKPGLTuRDAuUc9wO74FcyeZM-YYQ3Po1IDyyq90k1Ekd79OeHdsdp_HMpIIPDP';
    private $domain        = 'dev-1kz8p05uenan0uz6.us.auth0.com';
    private $audience      = 'https://dev-1kz8p05uenan0uz6.us.auth0.com/api/v2/';
    private $token;

    public function __construct()
    {
        $this->token = $this->getManagementToken();
    }

    private function getManagementToken()
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'audience' => $this->audience
        ];

        $ch = curl_init("https://{$this->domain}/oauth/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            return $result['access_token'];
        }

        throw new Exception("Auth0 token error: " . json_encode($result));
    }

    public function createUser($email, $password, $metadata = [])
    {
        if (!$this->token) {
            throw new Exception("No Auth0 token available");
        }

        $userData = [
            'email' => $email,
            'password' => $password,
            'connection' => 'Username-Password-Authentication'
        ];

        if (!empty($metadata)) {
            $userData['app_metadata'] = $metadata;
        }

        $ch = curl_init("https://{$this->domain}/api/v2/users");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer {$this->token}"
        ]);

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        curl_close($ch);

        if (isset($result['user_id'])) {
            return true;
        } elseif (isset($result['statusCode'])) {
            return [
                'status' => 'error',
                'code' => $result['statusCode'],
                'error' => $result['error'],
                'message' => $result['message'] ?? 'Unknown error'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Unexpected Auth0 API response'
            ];
        }
    }
}
