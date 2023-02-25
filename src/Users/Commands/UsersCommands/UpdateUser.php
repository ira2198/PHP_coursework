<?php 

namespace GeekBrains\LevelTwo\Users\Commands\UsersCommands;

use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
        {
        $this
        ->setName('user:update')
        ->setDescription('Updates a user')
        ->addArgument(
            'uuid',
            InputArgument::REQUIRED,
            'UUID of a user to update'
        )
        ->addOption(
            // Имя, cокращённое имя
            'user_name',
            'N',
            // Опция имеет значения
            InputOption::VALUE_OPTIONAL,
            // Описание
            'User name update',
        )
        ->addOption(
            'user_surname',
            's',
            InputOption::VALUE_OPTIONAL,
            'User surname update',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int 
    {
        // Получаем значения опций
        $userName = $input->getOption('user_name');
        $userSurname = $input->getOption('user_surname');

        // Выходим, если обе опции пусты
        if (empty($userName) && empty($userSurname)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        // Получаем UUID из аргумента
        $uuid = new UUID($input->getArgument('uuid'));

        // Получаем пользователя из репозитория
        $user = $this->usersRepository->get($uuid);

        // Создаём объект обновлённого имени
        $updatedUser= new User(
            uuid: $uuid, // оставляем как было
            login: $user->getLogin(),

            // Берём сохранённое имя, если опция имени пуста
            userName: empty($userName)
            ? $user->getUserName() : $userName,

            // Берём сохранённую фамилию, если опция фамилии пуста
            userSurname: empty($userSurname)
            ? $user->getUserSurname() : $userSurname,
            
            password: $user->getPassword()
        );
        // Создаём новый объект пользователя
        
        // Сохраняем обновлённого пользователя
        $this->usersRepository->save($updatedUser);
        $output->writeln("User updated: $uuid");
       
        return Command::SUCCESS;
        }       


}