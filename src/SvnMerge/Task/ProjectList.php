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

class ProjectList extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:list')
            ->setDescription('List all availables projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $main_config = File::convertPath(Config::get('config_dir', '/etc/svn-merge'));

        $projects = Project::getAll($main_config);

        $output->writeln('<fg=red;options=bold>Project list :</fg=red;options=bold>');
        foreach ($projects as $project) {
            $output->writeln('<fg=green;options=bold>  '.$project->getName().'</fg=green;options=bold>'.($project->getDescription() !== null ? ' : '.$project->getDescription() : ''));
        }
    }
}
