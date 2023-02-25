<?php

namespace GeekBrains\LevelTwo\Users\Commands\UsersCommands;

use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    // В родительском классе уже есть конструктор, чтобы не переопределить его расширим нашь через parent::...

     public function __construct(
        private UsersRepositoryInterface $usersRepository
     )
     {
        parent::__construct();
     }


     // Метод для конфигурации команды
   protected function configure(): void
   {
      $this
      // Указываем имя команды для запуска
      ->setName('user:create')
      // Описание команды
      ->setDescription('Creates new user')
      // Перечисляем аргументы команды

      ->addArgument('login', InputArgument::REQUIRED, 'Login')
      ->addArgument( 
         // Имя, значение будет доступно по имени
         'user_name',
         // Указание того, что аргумент обязательный
         InputArgument::REQUIRED,
         // Описание аргумента
         'User name'
      )
      // Описываем остальные аргументы
      ->addArgument('user_surname', InputArgument::REQUIRED, 'User surname')
      ->addArgument('password', InputArgument::REQUIRED, 'Password');
      }


      // Метод, запускается при вызове команды туда передастся объект типа InputInterface,
      // содержащий значения аргументов и объект типа OutputInterface,
      // имеющий методы для форматирования и вывода сообщений

      protected function execute( InputInterface $input, OutputInterface $output, ): int
      {
         // Для вывода сообщения вместо логгера объект типа OutputInterface
         $output->writeln('Create user command started');// вывод в консоль

         // Вместо использования нашего класса Arguments
         // получаем аргументы из объекта типа InputInterface
         $login = $input->getArgument('login');

         if ($this->userExists($login)) {
            // Используем OutputInterface вместо логгера
            $output->writeln("User already exists: $login");
            // Завершаем команду с ошибкой
            return Command::FAILURE; // работа прекращается
         } 

         // или из класса CreateUserCommand
         // Вместо Argv используем InputInterface
         $user = User::createFrom(
            $login,
            $input->getArgument('user_name'),
            $input->getArgument('user_surname'),
            $input->getArgument('password')      
         );
         
         $this->usersRepository->save($user);

         // Используем OutputInterface вместо логгера
         $output->writeln('User created: ' . $user->getUuid());
         // Возвращаем код успешного завершения
         return Command::SUCCESS;

      }


      // Проверка. перенесли из класса CreateUserCommand

      private function userExists(string $login): bool
      {
         try {
            $this->usersRepository->getByUserLogin($login);
         } catch (UserNotFoundExceptions) {
            return false;
         }
         return true;
   }
}