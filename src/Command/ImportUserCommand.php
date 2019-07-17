<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UserRepository;

class ImportUserCommand extends Command
{
    protected static $defaultName = 'app:import-user';
    private $userRepo;

    public function __construct(UserRepository $userRepo, string $name = null)
    {
        parent::__construct($name);
        $this->userRepo = $userRepo;
    }

    protected function configure()
    {
        $this
            ->setDescription('批量导入用户')
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, '文件路径')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getOption('file');

        if (empty($file)) {
            $io->error('请输入文件路径');
            return;
        }

        $fileHandle = fopen($file, 'r');
        $count = 0;

        while (($row = fgetcsv($fileHandle, 0, ',')) !== false) {
            $username = $row[0];
            $gender = $row[1];
            $region = $row[2];
            $phone = $row[3];
            
            $user = $this->userRepo->create($region, $phone, $username, 'xhlneice', $gender);
            if (!empty($row[4])) {
                $this->userRepo->edit($user, ['birthday' => new \Datetime($row[4])]);
            }

            dump($username);
            $count += 1;
        }

        $io->success("成功导入 $count 条数据");
    }
}
