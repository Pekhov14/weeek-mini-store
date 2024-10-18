<?php

namespace App\Command;

use App\DTO\ReportOrdersDto;
use App\Enum\ReportFileType;
use App\Enum\ReportTemplate;
use App\Message\GenerateOrdersReportMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:generate-order-report',
    description: 'Generates an order report.',
)]
class GenerateOrderReportCommand extends Command
{
    public function __construct(
        private MessageBusInterface $bus,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('preparedDate', InputArgument::REQUIRED, 'The string representing the report date')
            ->addOption('fileType', null, InputOption::VALUE_OPTIONAL, 'The file type for the report',  ReportFileType::JSON->value);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $preparedDateStr = $input->getArgument('preparedDate');
        $fileTypeStr = $input->getOption('fileType');

        try {
            $preparedDate = ReportTemplate::from($preparedDateStr);
            $fileType = ReportFileType::from($fileTypeStr);

            $reportOrderDto = new ReportOrdersDto($preparedDate, $fileType);
            $errors = $this->validator->validate($reportOrderDto);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $io->error((string) $error);
                }
                return Command::FAILURE;
            }

            $message = new GenerateOrdersReportMessage($reportOrderDto);
            $this->bus->dispatch($message);

            $io->success('Report generation has been dispatched successfully.');
            return Command::SUCCESS;
        } catch (\ValueError $e) {
            $io->error('Invalid value for preparedDate or fileType: ' . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
