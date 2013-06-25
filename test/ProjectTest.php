<?php
/**
 * This file is part of the SvnMerge package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ProjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \SvnMerge\Project::getAll
     * @covers  \SvnMerge\Project::__construct
     */
    public function testGetAll()
    {
        $projects = \SvnMerge\Project::getAll(__DIR__.DIRECTORY_SEPARATOR.'config');

        // Two projects are ready
        $this->assertCount(2, $projects, 'We must be have 2 projects');

        // All items must be Project instance
        foreach ($projects as $project) {
            $this->assertInstanceOf('\\SvnMerge\\Project', $project);
        }
    }


    /**
     * @covers  \SvnMerge\Project::__construct
     * @covers  \SvnMerge\Project::getName
     * @covers  \SvnMerge\Project::getAvailableDirections
     * @covers  \SvnMerge\Project::getUsername
     * @covers  \SvnMerge\Project::getPassword
     * @covers  \SvnMerge\Project::getDirection
     * @covers  \SvnMerge\Project::getBaseDir
     * @covers  \SvnMerge\Project::getSvnRepository
     * @covers  \SvnMerge\Project::getDescription
     * @covers  \SvnMerge\Project::parseFile
     * @covers  \SvnMerge\Project::parseUserConfig
     */
    public function testLoadProject()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'example.ini', 
            __DIR__.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'user.ini'
        );

        $this->assertInstanceOf('\\SvnMerge\\Project', $project);
        $this->assertEquals('example', $project->getName());
        $this->assertEquals(array('preprod', 'prod'), $project->getAvailableDirections());
        $this->assertEquals('my-username', $project->getUsername());
        $this->assertEquals('my-password', $project->getPassword());
        $this->assertInstanceOf('stdClass', $project->getDirection('preprod'));
        $this->assertEquals('Merge some commit from trunk into preprod branch', $project->getDirection('preprod')->description);
        $this->assertEquals('~/sd/example/www/', $project->getBaseDir());
        $this->assertEquals('svn://localhost/example/', $project->getSvnRepository());
        $this->assertEquals('An example project', $project->getDescription());
    }


    /**
     * @covers  \SvnMerge\Project::__construct
     * @covers  \SvnMerge\Project::getName
     * @covers  \SvnMerge\Project::getUsername
     * @covers  \SvnMerge\Project::getPassword
     * @covers  \SvnMerge\Project::parseFile
     * @covers  \SvnMerge\Project::parseUserConfig
     */
    public function testLoadProjectWithoutUserConfig()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'example.ini'
        );

        $this->assertInstanceOf('\\SvnMerge\\Project', $project);
        $this->assertEquals('example', $project->getName());
        $this->assertNull($project->getUsername());
        $this->assertNull($project->getPassword());
    }


    /**
     * @expectedException \SvnMerge\Exception\File
     * @covers  \SvnMerge\Project::__construct
     * @covers  \SvnMerge\Project::parseFile
     */
    public function testFailIniProject()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'bad-example.ini'
        );
    }


    /**
     * @expectedException \SvnMerge\Exception\File
     * @covers  \SvnMerge\Project::__construct
     * @covers  \SvnMerge\Project::parseUserConfig
     */
    public function testFailIniUser()
    {
        $project = new \SvnMerge\Project(
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'example.ini',
            __DIR__.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'bad-user.ini'
        );
    }
}