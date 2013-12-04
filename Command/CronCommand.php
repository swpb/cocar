<?php

namespace Swpb\Bundle\CocarBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends Command
{
    /**
     *
     * @var type object
     */
    private $em;
    
    protected function configure()
    {
        $this
            ->setName('perform:task')
            ->setDescription('Run scheduled tasks')
            ->addOption(
               'monitor',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'status',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'reliability',
               null,
               InputOption::VALUE_NONE
            )            
            ->addOption(
               'rrdlog',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'dailyperform',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'generatealarm',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'endalarm',
               null,
               InputOption::VALUE_NONE
            )
            ->addOption(
               'graphdailyperform',
               null,
               InputOption::VALUE_NONE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getApplication()->getKernel()->getContainer()->get('doctrine')->getManager();
        
        if ($input->getOption('monitor')) {
            $return = $this->performTask('monitor');
        }
        elseif($input->getOption('status')) {
            $return = $this->performTask('status');
        }
        elseif($input->getOption('reliability')) {
            $return = $this->performTask('reliability');
        }
        elseif($input->getOption('rrdlog')) {
            $return = $this->performTask('rrdlog');
        }
        elseif($input->getOption('dailyperform')) {
            $return = $this->performTask('dailyperform');
        }
        elseif($input->getOption('generatealarm')) {
            $return = $this->performTask('generatealarm');
        }
        elseif($input->getOption('endalarm')) {
            $return = $this->performTask('endalarm');
        }
        elseif($input->getOption('graphdailyperform')) {
            $return = $this->performTask('graphdailyperform');
        }

        $output->writeln($return);
    }

    private function performTask($task)
    {
        try
        {
            $r = file_get_contents($this->getApplication()->getKernel()->getContainer()->get('router')
                ->generate("cocar_$task", array(), true));

            echo $r;die;

            if($task == 'monitor')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/monitor");
            elseif($task == 'status')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/status");
            elseif($task == 'reliability')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/reliability");
            elseif($task == 'rrdlog')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/rrdlog");
            elseif($task == 'dailyperform')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/dailyperform");
            elseif($task == 'generatealarm')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/generatealarm");
            elseif($task == 'endalarm')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/endalarm");
            elseif($task == 'graphdailyperform')
                file_get_contents("http://localhost/projeto-cocar/web/app_dev.php/cocar/graphdailyperform");
        }
        catch(Exception $e)
        {
            return "An unexpected: $task";
        }
        return "Task executed: $task";
    }
}
