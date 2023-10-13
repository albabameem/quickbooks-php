<?php
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;

/** @var \Laravel\Lumen\Routing\Router $router */


$router->get("/init", function (\Illuminate\Http\Request $request) {
    $oauth2LoginHelper = new OAuth2LoginHelper(
        env("clientId"),
        env("clientSecret"),
        env("redirectUrl"),
        env("scope"),
        env("state", null)
    );
    $authorizationCodeUrl = $oauth2LoginHelper->getAuthorizationCodeURL();

    echo "<script>window.location.href='" .
        $authorizationCodeUrl .
        "';</script>";
});

$router->get("/callback", function (\Illuminate\Http\Request $request) {
    $code = (string) request()->query("code");
    $realmId = (string) request()->query("realmId");

    $dataService = DataService::Configure([
        "auth_mode" => "oauth2",
        "grant_type" => "authorization_code",
        "ClientID" => env("clientId"),
        "ClientSecret" => env("clientSecret"),
        "RedirectURI" => env("redirectUrl"),
        "scope" => env("scope"),
        "state" => env("state"),
        "baseUrl" => env("baseUrl"),
    ]);
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

    $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken(
        $code,
        $realmId
    );
    $dataService->updateOAuth2Token($accessTokenObj);

    $path = base_path(".env");

    file_put_contents(
        $path,
        str_replace(
            'accessToken="' . env("accessToken") . '"',
            'accessToken="' . $accessTokenObj->getAccessToken() . '"',
            file_get_contents($path)
        )
    );

    file_put_contents(
        $path,
        str_replace(
            'refreshToken="' . env("refreshToken") . '"',
            'refreshToken="' . $accessTokenObj->getRefreshToken() . '"',
            file_get_contents($path)
        )
    );

    echo "<script>window.location.href='refresh';</script>";
});

$router->get("/refresh", function (\Illuminate\Http\Request $request) {
    $dataService = DataService::Configure([
        "auth_mode" => "oauth2",
        "grant_type" => "refresh_token",
        "ClientID" => env("clientId"),
        "ClientSecret" => env("clientSecret"),
        "RedirectURI" => env("redirectUrl"),
        "refreshTokenKey" => env("refreshToken"), //$request->session()->get('refreshToken'),
        "QBORealmID" => env("realmId"),
        "scope" => env("scope"),
        "state" => env("state"),
        "baseUrl" => env("baseUrl"),
    ]);
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
    $dataService->updateOAuth2Token($refreshedAccessTokenObj);

    $path = base_path(".env");

    file_put_contents(
        $path,
        str_replace(
            'accessToken="' . env("accessToken") . '"',
            'accessToken="' . $refreshedAccessTokenObj->getAccessToken() . '"',
            file_get_contents($path)
        )
    );

    file_put_contents(
        $path,
        str_replace(
            'refreshToken="' . env("refreshToken") . '"',
            'refreshToken="' .
                $refreshedAccessTokenObj->getRefreshToken() .
                '"',
            file_get_contents($path)
        )
    );
    echo "Success! Make sure to add the following cron job: <br /><strong>*/15 * * * * wget --quiet -O /dev/null https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]</strong> <br /> <br /><a href='customer'>Try the fetch customers api</a>";
});

$router->get("/customer", function (\Illuminate\Http\Request $request) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL =>
            "https://" .
            env("baseUrl") .
            "/v3/company/" .
            env("realmId") .
            "/query?minorversion=14",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => 'Select * from Customer Where Metadata.LastUpdatedTime > \'2015-03-01\' Maxresults 5',
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/text",
            "Authorization: Bearer " . env("accessToken"),
            "Cookie: ivid=6f3712cf-a567-4dd7-befc-66114f88906a",
        ],
    ]);

    $response = curl_exec($curl);
    $response = json_decode($response);

    curl_close($curl);
    print_r($response);
});
