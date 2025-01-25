<?php

namespace App\Repository;

use  App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;


class UserRepository extends AbstractRepository implements UserRepositoryInterface
{

    public function __construct()
    {
        $this->model = User::class;
    }

    /**
     * Insere um novo usuário no banco de dados.
     *
     * @param string $cognitoUserId
     * @param string $email
     * @param string $password
     * @param string $name
     * @param array $data
     * @return string
     */
    public function insert(string $cognitoUserId, string $email, string $password, string $name, $data): string
    {
        try {
            User::create([
                'cognito_user_id' => $cognitoUserId,
                'email' => $email,
                'password' =>  Hash::make($password),
                'email_verified_at' => Carbon::now('America/Sao_Paulo'),
                'name' => $name,
            ]);

            return 'user registered successfully!';
        } catch (\Exception $e) {
            return 'error inserting user: ' . $e->getMessage();
        }
    }
    
    public function findByEmail(string $email)
    {
        return $this->find('email', $email);
    }

    
    public function findBytoken(string $token)
    {
        return $this->find('token', $token);
    }

    /**
     * Atualiza os dados de acesso do usuário no banco de dados.
     *
     * @param string $email O email do usuário.
     * @param Result $response A resposta do AWS Cognito.
     * @return void
     */
    public function update(string $email, $response): void
    {
        try {
            $user = $this->findByEmail($email);

            if (!$user) {
                throw new \Exception("User with email {$email} not found.");
            }

            $expiresIn = $response['AuthenticationResult']['ExpiresIn'];
            $expirationDatetime = Carbon::now()->addSeconds($expiresIn);
            $user->update([
                'token' => $response['AuthenticationResult']['AccessToken'] ?? NULL,
                'expired_date' => $expirationDatetime,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('error updating user: ' . $e->getMessage());
        }
    }

}
