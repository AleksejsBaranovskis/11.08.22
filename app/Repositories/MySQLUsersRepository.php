<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Exceptions\RecordNotFoundException;

class MySQLUsersRepository implements UsersRepository
{
    private \Doctrine\DBAL\Connection $connection;

    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'users',
            'user' => $_ENV['MYSQL_USER'],
            'password' => $_ENV['MYSQL_PASSWORD'],
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ];
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
    }

    public function save(User $user): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->insert('users')
            ->values([
                'username' => ':username',
                'email' => ':email',
                'password' => ':password'
            ])
            ->setParameters([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
            ])->executeQuery();
    }

    public function getByUsername(string $username): User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $user = $queryBuilder->select('*')
            ->from('users')
            ->where('username= :username')
            ->setParameter('username', $username)
            ->fetchAssociative();

        if (!$user) {
            throw new RecordNotFoundException("User with username $username was not found.");
        }

        return new User(
            $user['username'],
            $user['email'],
            $user['password'],
            $user['id']
        );
    }
}