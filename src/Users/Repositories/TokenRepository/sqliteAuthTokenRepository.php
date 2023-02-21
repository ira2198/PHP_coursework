<?php

namespace GeekBrains\LevelTwo\Users\Repositories\TokenRepository;

use DateTimeImmutable;
use DateTimeInterface;
use GeekBrains\LevelTwo\Users\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\AuthTokensRepositoryException;
use GeekBrains\LevelTwo\Users\UUID;
use PDO;
use PDOException;


class sqliteAuthTokenRepository implements AuthTokenRepoInterface 
{
    public function __construct( private PDO $connectDB )
    {         
    }


    public function saveToken(AuthToken $authToken): void
    {
        $query = <<<SQL
            INSERT INTO tokens (token, user_uuid, expires)
            VALUES (:token, :user_uuid, :expires)
            ON CONFLICT (token) 
            DO UPDATE SET expires = :expires
        SQL;

        try {
            $statement = $this->connectDB->prepare($query);
            $statement->execute([
                ':token' => $authToken->getToken(),
                ':user_uuid' => (string)$authToken->getUserUuid(),
                ':expires' => $authToken->getExpiresOn()
                    ->format(DateTimeInterface::ATOM) //cтрока текущего времени

            ]);
        } catch (PDOException $err) {
            throw new AuthTokensRepositoryException(
                $err->getMessage(), (int)$err->getCode(), $err
            );
        }

    }

    public function getToken(string $token): AuthToken
    {
        try {
            $statement = $this->connectDB->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );

            $statement->execute([$token]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);

        } catch (PDOException $err) {
            throw new AuthTokensRepositoryException(
            $err->getMessage(), (int)$err->getCode(), $err
            );
        }

        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires'])
            );
        } catch (\Exception $err) {
            throw new AuthTokensRepositoryException(
            $err->getMessage(), $err->getCode(), $err
            );
        }            
    }
}
