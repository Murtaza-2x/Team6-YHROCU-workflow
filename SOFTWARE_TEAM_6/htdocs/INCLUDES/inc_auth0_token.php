<?php
function getManagementToken()
{
    $client_id = 'm2pLRjG1FbG3JjA2Zv8xnzlLfqblk68s';
    $client_secret = 'O1IKPGLTuRDAuUc9wO74FcyeZM-YYQ3Po1IDyyq90k1Ekd79OeHdsdp_HMpIIPDP';

    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'audience' => 'https://dev-1kz8p05uenan0uz6.us.auth0.com/api/v2/',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://dev-1kz8p05uenan0uz6.us.auth0.com/oauth/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $api_result = curl_exec($ch);
    curl_close($ch);

    $api_result = json_decode($api_result, true);

    if (!isset($api_result['access_token'])) {
        var_dump($api_result); //
        return null;
    }

    return $api_result['access_token'];
}