<?php

/**
 * This file is part of the SvnMerge package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SvnMerge\Task;

use SvnMerge\Config;
use SvnMerge\Project;
use SvnMerge\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ProjectShow extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:show')
            ->setDescription('Show the details of a project')
            ->addArgument('project', InputArgument::REQUIRED, 'The name of the project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $main_config = File::convertPath(Config::get('config_dir', '/etc/svn-merge').'/'.$input->getArgument('project').'.ini');

        $project = new Project($main_config);

        $output->writeln('<fg=red;options=bold>'.$project->getName().'</fg=red;options=bold>');
        $output->writeln('<fg=black;options=bold>Directions :</fg=black;options=bold>');
        foreach ($project->getAvailableDirections() as $direction) {
            $datas = $project->getDirection($direction);

            $output->writeln('<info>  '.$direction.'</info> : '.($datas->description !== null ? $datas->description : $datas->message));
        }
    }
}
