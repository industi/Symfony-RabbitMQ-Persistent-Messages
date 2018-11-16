<?php

namespace App\Command;

use App\Message\TestMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTestMessageCommand extends Command
{
    protected static $defaultName = 'CreateTestMessageCommand';
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct($name = null, MessageBusInterface $messageBus)
    {
        parent::__construct($name);
        $this->messageBus = $messageBus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create TestMessage command')
            ->addOption('size', '--size', InputOption::VALUE_OPTIONAL, 'Amount of messages')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $amount = 1;

        if ($input->getOption('size')) {
            if (!preg_match('/^[0-9]+$/', $input->getOption('size'))) {
                throw new \InvalidArgumentException('Invalid value of size argument');
            }

            $amount = $input->getOption('size');
        }

        for ($i = 1; $i <= $amount; $i++) {
            $this->messageBus->dispatch(new TestMessage(sprintf('Message: %s', $i)));
        }

        $io->success("Created $amount of TestMessages");
    }
}
