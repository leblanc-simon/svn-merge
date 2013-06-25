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

use SvnMerge\Svn;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class Run extends BaseCommand
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('command:run')
            ->setDescription('Run the svn merge command');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->svn->run();
    }
}