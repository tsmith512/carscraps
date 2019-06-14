<?php

namespace App\Command;
use App\Service\CarScrapsFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CarScrapsFetchCommand extends Command
{
    protected static $defaultName = 'carscraps:fetch';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function __construct(CarScrapsFetcher $fetcher) {
        $this->fetcher = $fetcher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$howMany = $this->fetcher->unfetchedCars()) {
            $io->success('There are no unfetched cars in the mirroring queue.');
        } else {
            $io->text("There are {$howMany} cars in the queue.");
        }
    }
}
