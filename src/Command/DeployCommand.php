<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeployCommand extends Command
{
    protected static $defaultName = 'app:deploy';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Deploy command via CLI')
            ->addArgument('server-type', InputArgument::OPTIONAL, 'dev or prod')
            ->addOption('branch', null, InputArgument::OPTIONAL, 'Set git branch', 'master')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io         = new SymfonyStyle($input, $output);
        $config     = $this->container->getParameter('deploy');
        $serverType = $input->getArgument('server-type');
        $config     = $config[$serverType];
        $connection = ssh2_connect($config['host']);

        if ($serverType) {
            $io->note(sprintf('You passed an argument: %s', $serverType));
        }

        ssh2_auth_password($connection, $config['username'], $config['password']);

        $stream = ssh2_exec($connection,
            'cd /var/www/hire;' .
            'git pull origin ' . $input->getOption('branch') . ';' .
            'composer install;' .
            'php bin/console doctrine:schema:update --force;' .
            'php bin/console cache:clear && sudo chmod 777 -R var/cache/;' .
            'sudo service php7.3-fpm restart;' .
            'sudo service nginx restart;'
        );

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        // Enable blocking for both streams
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $io->text(stream_get_contents($stream));
        $io->text(stream_get_contents($errorStream));

        // Close the streams
        fclose($errorStream);
        fclose($stream);

        $io->success('Yay! You are finished deploy command');
    }
}
