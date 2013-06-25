<?php
/**
 * This file is part of the SvnMerge package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SvnTest extends PHPUnit_Framework_TestCase
{
    public function testShowWithCredential()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'example.ini', 
            __DIR__.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'user.ini'
        );

        $svn = new \SvnMerge\Svn($project, 'prod', '1-5,10,100');

        $message =  'cd ~/sd/example/www/ && \\'."\n".
                    'svn update --username=\'my-username\' --password=\'my-password\' && \\'."\n".
                    'cd ~/sd/example/www/branches/prod/ && \\'."\n".
                    'svn merge -c1,2,3,4,5,10,100 svn://localhost/example/branches/preprod --username=\'my-username\' --password=\'my-password\' && \\'."\n".
                    'svn commit --username=\'my-username\' --password=\'my-password\' -m \'Merge des revisions [1], [2], [3], [4], [5], [10], [100] (preprod into prod)\'';

        $this->assertEquals($message, $svn->show());
    }


    public function testShowWithoutCredential()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'example2.ini'
        );

        $svn = new \SvnMerge\Svn($project, 'prod', '1-5,10,99');

        $message =  'cd ~/sd/example2/www/ && \\'."\n".
                    'svn update && \\'."\n".
                    'cd ~/sd/example2/www/branches/prod/ && \\'."\n".
                    'svn merge -c1,2,3,4,5,10,99 svn://localhost/example2/branches/preprod && \\'."\n".
                    'svn commit -m \'Merge des revisions [1], [2], [3], [4], [5], [10], [99] (preprod into prod)\'';

        $this->assertEquals($message, $svn->show());
    }
}