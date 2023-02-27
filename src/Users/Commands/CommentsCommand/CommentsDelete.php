<?php


namespace GeekBrains\LevelTwo\Users\Commands\CommentsCommand;

use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CommentsDelete  extends Command
{
    public function __construct(
        private CommentsRepositoryInterface $commentRepository
    )
    {
        parent::__construct();        
    }
    
    protected function configure(): void
    {
        $this
            ->setName('comment:delete')
            ->setDescription('Deletes a comment')
            ->addArgument('uuid', InputArgument::REQUIRED, 'UUID of a comment to delete')
           
            ->addOption(
               'check-existence',
               'c',
                InputOption::VALUE_NONE,
                'Check if comment actually exists'
        );
    }



    protected function execute( InputInterface $input, OutputInterface $output, ): int
    {
        $question = new ConfirmationQuestion(
            'Delete Comment [Y/n]? ',
            false
        );
       
        if (
            !$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {      
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));
       
        if ($input->getOption('check-existence')) {
            try {              
                 $this->commentRepository->get($uuid);
            } catch (CommentNotFoundException $err) {
                // Выходим, если статья не найдена
                $output->writeln($err->getMessage());
                return Command::FAILURE;
            }
        }    

        // Удаляем статью из репозитория
        $this->commentRepository->delete($uuid);
        $output->writeln("Comment $uuid deleted");

        return Command::SUCCESS;
    }
}
