<?php

/**
 * This file is part of the SvnMerge package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SvnMerge;

use Symfony\Component\Console\Application;

class Main
{
    public static function run()
    {
        $application = new Application();

        $application->add(new Task\Show());
        $application->add(new Task\Run());
        $application->add(new Task\ProjectList());
        $application->add(new Task\ProjectShow());

        $application->run();
    }
}
