<?php

namespace GeekBrains\LevelTwo\Users\Commands\PostsCommands;

use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PostDelete extends Command
{

    public function __construct(
        private PostRepositoryInterface $postsRepository
    )
    {
        parent::__construct();
    }

    // Конфигурации команды
    protected function configure(): void
    {
        $this
            ->setName('post:delete')
            ->setDescription('Deletes a post')
            ->addArgument('uuid', InputArgument::REQUIRED, 'UUID of a post to delete')
            // Добавим опцию проверить существование поста
            ->addOption(
                // Имя опции
                'check-existence',
                // Сокращённое имя
                'c',
                // Опция не имеет значения
                InputOption::VALUE_NONE,
                // Описание
                'Check if post actually exists'
        );
    }


    protected function execute( InputInterface $input, OutputInterface $output, ): int
    {
        $question = new ConfirmationQuestion(
            // Вопрос для подтверждения
            'Delete post [Y/n]? ',
            // По умолчанию не удалять
            false
        );
        
        // Ожидаем подтверждения
        if (
            !$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {
        // Выходим, если удаление не подтверждено
            return Command::SUCCESS;
        }

        // Получаем UUID статьи
        $uuid = new UUID($input->getArgument('uuid'));

        // Если опция проверки существования статьи установлена
        if ($input->getOption('check-existence')) {
            try {
                // Пытаемся получить статью
                 $this->postsRepository->get($uuid);
            } catch (PostNotFoundException $err) {
                // Выходим, если статья не найдена
                $output->writeln($err->getMessage());
                return Command::FAILURE;
            }
        }
    

        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);
        $output->writeln("Post $uuid deleted");

        return Command::SUCCESS;
    }
}
// Пр. php cli.php post:delete d19edf7a-5061-4eee-82d7-4eca3b9076d2 -c
