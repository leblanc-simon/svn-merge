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

use Symfony\Component\Finder\Finder;

class Project
{
    private $file = null;
    private $user_config = null;

    private $name = null;

    private $directions = array();
    private $base_dir = null;
    private $svn_repository = null;
    private $description = null;

    private $username = null;
    private $password = null;

    public function __construct($file, $user_config = null)
    {
        $this->file = $file;
        $this->name = pathinfo($file, PATHINFO_FILENAME);

        $this->user_config = $user_config;

        $this->parseFile();
        $this->parseUserConfig();
    }

    /**
     * Return the availables directions names.
     *
     * @return array the availables directions names
     */
    public function getAvailableDirections()
    {
        return array_keys($this->directions);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDirection($direction)
    {
        if (isset($this->directions[$direction]) === false) {
            throw new Exception\Project($direction.' isn\'t available');
        }

        return $this->directions[$direction];
    }

    public function getBaseDir()
    {
        return $this->base_dir;
    }

    public function getSvnRepository()
    {
        return $this->svn_repository;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Parse all availables projects and return them.
     *
     * @param string $main_config_dir The main configuration directory to parse
     * @param string|null $user_config The user configuration to load
     *
     * @return array<Project> An array with all availables projects
     * @static
     */
    public static function getAll($main_config_dir, $user_config = null)
    {
        $main_finder = new Finder();
        $main_finder
            ->files()
            ->name('*.ini')
            ->sortByName()
            ->depth('== 0')
            ->in($main_config_dir);

        $projects = array();
        foreach ($main_finder as $file) {
            $projects[] = new self($file->getRealpath(), $user_config);
        }

        return $projects;
    }

    /**
     * Parse the ini file to get the informations.
     *
     * @throws \SvnMerge\Exception\File If the file isn't readable
     * @throws \SvnMerge\Exception\Ini  If the file isn't a ini file
     * @throws \SvnMerge\Exception\Ini  If the ini doesn't contains basic informations
     * @throws \SvnMerge\Exception\Ini  If the ini doesn't contains direction informations
     */
    private function parseFile()
    {
        if (file_exists($this->file) === false || is_readable($this->file) === false) {
            throw new Exception\File($this->file.' isn\'t readable');
        }

        $ini = parse_ini_file($this->file, true);

        if (is_array($ini) === false || count($ini) === 0) {
            throw new Exception\Ini('Impossible to parse ini file : '.$this->file);
        }

        if (isset($ini['common']) === false || isset($ini['common']['base']) === false || isset($ini['common']['svn']) === false) {
            throw new Exception\Ini('The ini file "'.$this->file.'" hasn\'t the good format');
        }

        foreach ($ini as $direction => $datas) {
            if ($direction === 'common') {
                $this->base_dir = $datas['base'];
                $this->svn_repository = $datas['svn'];
                $this->description = (isset($datas['description']) === true) ? $datas['description'] : null;
            } else {
                if (isset($datas['dir']) === false || isset($datas['from']) === false || isset($datas['message']) === false) {
                    throw new Exception\Ini('The direction '.$direction.' isn\'t in the good format');
                }

                $direction_object = new \stdClass();
                $direction_object->dir = $datas['dir'];
                $direction_object->from = $datas['from'];
                $direction_object->message = $datas['message'];
                $direction_object->description = (isset($datas['description']) === true) ? $datas['description'] : null;

                $this->directions[$direction] = $direction_object;
            }
        }
    }

    /**
     * Parse the ini file to get the user informations.
     *
     * @throws \SvnMerge\Exception\File If the file isn't readable
     * @throws \SvnMerge\Exception\Ini  If the file isn't a ini file
     * @throws \SvnMerge\Exception\Ini  If the ini doesn't contains auth informations
     */
    private function parseUserConfig()
    {
        $this->username = null;
        $this->password = null;

        if ($this->user_config === null) {
            return;
        }

        if (file_exists($this->user_config) === false || is_readable($this->user_config) === false) {
            throw new Exception\File($this->user_config.' isn\'t readable');
        }

        $ini = parse_ini_file($this->user_config, true);

        if (is_array($ini) === false || count($ini) === 0) {
            throw new Exception\Ini('Impossible to parse ini file : '.$this->user_config);
        }

        if (isset($ini['auth']) === false) {
            throw new Exception\Ini('Impossible to find auth section in the ini file : '.$this->user_config);
        }

        if (isset($ini['auth']['username']) === true) {
            $username = trim($ini['auth']['username']);
            if (empty($username) === false) {
                $this->username = $username;
            }
        }

        if (isset($ini['auth']['password']) === true) {
            $password = trim($ini['auth']['password']);
            if (empty($password) === false) {
                $this->password = $password;
            }
        }
    }
}
