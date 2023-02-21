<?php
namespace GeekBrains\LevelTwo\Users\Repositories;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;

// Dummy - чучуло, манекен


class DummyUsersRepository implements UsersRepositoryInterface
{
public function save(User $user): void
{
// Ничего не делаем
}
public function get(UUID $uuid): User
{
// И здесь ничего не делаем
throw new UserNotFoundExceptions("Not found");
}
public function getByUserLogin(string $login): User
{
    
// Нас интересует реализация только этого метода
// Для нашего теста не важно, что это будет за пользователь,
// поэтому возвращаем совершенно произвольного
return new User(UUID::random(), "Ivan", "Ivan", "Nikitin", "123");
}
}
