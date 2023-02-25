<?php

namespace GeekBrains\LevelTwo\Users\Commands\FakeData;

use Faker\Generator;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postRepository,
        private Generator $faker
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setName('fake-data:populate-db')
        ->setDescription('Populates DB with fake data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int 
    {
        // Создаём десять пользователей
        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getLogin());
        }

            // От имени каждого пользователя создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < 20; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }
    return Command::SUCCESS;
}

    private function createFakeUser(): User
    {
        $user = User::createFrom(
        // Генерируем данные пользователя
        $this->faker->userName,
        $this->faker->firstName,
        $this->faker->lastName,
        $this->faker->password
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }


    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,            
            $this->faker->sentence(6, true),  // Генерируем предложение не длиннее шести слов
            $this->faker->realText
        );
 
        // Сохраняем статью в репозиторий
        $this->postRepository->save($post);
        return $post;
    }
}