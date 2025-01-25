<?php

namespace App\Services;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use App\Contracts\Repositories\{UserRepositoryInterface};

use Aws\Exception\AwsException;

class CognitoService
{
    protected $client;
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->client = new CognitoIdentityProviderClient([
            'region' => config('services.cognito.region'),
            'version' => '2016-04-18',
            'credentials' => [
                'key' => config('services.cognito.key'),
                'secret' => config('services.cognito.secret'),
            ],
        ]);
    }


    public function registerUser($email, $password, $name = null)
    {

        try {          
            $secretHash = $this->generateSecretHash($email);
            $response = $this->client->signUp([
                'ClientId' => config('services.cognito.client_id'),
                'Username' => $email,
                'Password' => $password,
                'SecretHash' => $secretHash,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $email],
                    ['Name' => 'name', 'Value' => $name],
                ],
            ]);

            $cognitoUserId =  isset($response['UserSub']) ? $response['UserSub'] : null;

            $cognitoUserId
                ? $this->userRepositoryInterface->insert($cognitoUserId, $email, $password, $name, $response)
                : throw new \Exception('Cognito User ID is null. User registration failed.');

            return $response;
        } catch (AwsException $e) {
            
            return $e->getMessage();
        }
    }

    public function authenticateUser($email, $password)
    {
        try {
            $secretHash = $this->generateSecretHash($email);

            $response = $this->client->initiateAuth([
                'AuthFlow' => 'USER_PASSWORD_AUTH',
                'ClientId' => config('services.cognito.client_id'),
                'AuthParameters' => [
                    'USERNAME' => $email,
                    'PASSWORD' => $password,
                    'SECRET_HASH' => $secretHash,
                ],
            ]);
            $this->userRepositoryInterface->update($email, $response);

            return $response['AuthenticationResult'];
        } catch (\Aws\Exception\AwsException $e) {
            return $e->getMessage();
        }
    }

    private function generateSecretHash($email)
    {
        return base64_encode(hash_hmac(
            'sha256',
            $email . config('services.cognito.client_id'),
            config('services.cognito.client_secret'),
            true
        ));
    }
}
