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
use SvnMerge\Svn;
use SvnMerge\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BaseCommand extends Command
{
    private $project_name = null;
    private $project = null;
    private $direction = null;
    private $commits = null;

    protected $svn = null;

    protected function configure()
    {
        $this
            ->addArgument('project', InputArgument::REQUIRED, 'The name of the project')
            ->addArgument('direction', InputArgument::REQUIRED, 'The direction of the merge')
            ->addArgument('commits', InputArgument::REQUIRED, 'The list of the commits to merge (format : 1,3,6,8-10)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->project_name = $input->getArgument('project');
        $this->direction = $input->getArgument('direction');
        $this->commits = $input->getArgument('commits');

        $main_config = File::convertPath(Config::get('config_dir', '/etc/svn-merge').'/'.$this->project_name.'.ini');
        $user_config = File::convertPath(Config::get('user_config', '~/.svn-merge/user.ini'));

        $this->project = new Project($main_config, file_exists($user_config) ? $user_config : null);

        $this->svn = new Svn($this->project, $this->direction, $this->commits);
    }
}
