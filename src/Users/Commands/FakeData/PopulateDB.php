<?php

namespace GeekBrains\LevelTwo\Users\Commands\FakeData;

use Faker\Generator;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postRepository,
        private CommentsRepositoryInterface $commentRepository,
        private Generator $faker
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setName('fake-data:populate-db')
        ->setDescription('Populates DB with fake data')


        ->addOption(
           'users-number',
           // сокращение имени
           'u',
            // этот тип опции требует значениe
            InputOption::VALUE_REQUIRED,
            // описание опции
            'How many users to create',
            // значение опции по умолчанию
            1,
        )

        ->addOption(
           'posts-number',
           'p',
            InputOption::VALUE_REQUIRED,
            'How many posts should each user create',
            1,
        )

        ->addOption(
            'comments-number',
            'c',
             InputOption::VALUE_REQUIRED,
             'Hhow many comments should there be for each post',
             1,
         );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int 
    {

        // Создаём десять пользователей
        $users = [];
        for ($i = 0; $i < $input->getOption('users-number'); $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getLogin());
        }

        $posts = [];
            // От имени каждого пользователя создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < $input->getOption('posts-number'); $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        // Для каждого поста создаем комментарии
        foreach ($posts as $post) {
            for ($i = 0; $i < $input->getOption('comments-number'); $i++) {
                $comment = $this->createComments($user, $post);
                $output->write('Comment number ' . $comment->getUuid() . ' created');
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

    private function createComments(User $author, Post $article): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $article,
            $this->faker->realText(50)
        );

        $this->commentRepository->save($comment);
        return $comment;
    }

}