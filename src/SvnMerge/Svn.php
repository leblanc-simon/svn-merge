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

class Svn
{
    private $project = null;
    private $direction = null;

    private $commit_number_message = '';
    private $commit_number_merge = '';

    private $commands = array();

    public function __construct(Project $project, $direction, $commits)
    {
        $this->project = $project;
        $this->direction = $direction;

        $this->generateCommitNumber($commits);
        $this->generateCommands($direction);
    }


    /**
     * Return the commands to execute for merge commits
     * 
     * @return  string      the commands to execute
     * @access  public
     */
    public function show()
    {
        return implode(' && \\'."\n", $this->commands);
    }


    /**
     * Execute the commands to apply the merge (update - merge - commit)
     * 
     * @throws  \SvnMerge\Exception\Command     If a command failed
     * @access  public
     */
    public function exec()
    {
        foreach ($this->commands as $command) {
            if (substr($command, 0, 5) === 'cd ~/') {
                $command = 'cd '.File::convertPath(substr($command, 3));
            }

            passthru($command, $return);
            if ($return !== 0) {
                throw new Exception\Command('Fail to execute '.$command);
            }
        }
    }


    /**
     * Generate the list of the commit wanted in the merge 
     * (for merge command and the message)
     *
     * @param   string  $commits    All commit numbers wanted (1-5,10,100 format)
     * @access  private
     */
    private function generateCommitNumber($commits)
    {
        $this->commit_number_merge = '';
        $this->commit_number_message = '';

        $blocks = explode(',', $commits);

        foreach ($blocks as $block) {
            $interval = explode('-', $block);

            if (count($interval) === 1) {
                $this->commit_number_merge .= $block.',';
                $this->commit_number_message .= '['.$block.'], ';
            } else {
                foreach (range($interval[0], $interval[1]) as $rev) {
                    $this->commit_number_merge .= $rev.',';
                    $this->commit_number_message .= '['.$rev.'], ';
                }
            }
        }

        $this->commit_number_merge = substr($this->commit_number_merge, 0, -1);
        $this->commit_number_message = substr($this->commit_number_message, 0, -2);
    }


    /**
     * Generate all commands required for a merge
     *
     * @access  private
     */
    private function generateCommands()
    {
        $this->commands = array();

        $base = $this->project->getBaseDir();
        $svn = $this->project->getSvnRepository();

        $direction = $this->project->getDirection($this->direction);
        $dir = $direction->dir;
        $from = $direction->from;
        $message = $direction->message;

        $login_pass = $this->buildLogin();

        $this->commands[] = 'cd '.$base;
        $this->commands[] = 'svn update'.$login_pass;
        $this->commands[] = 'cd '.$base.$dir;
        $this->commands[] = 'svn merge -c'.$this->commit_number_merge.' '.$svn.$from.$login_pass;
        $this->commands[] = 'svn commit'.$login_pass.' -m '.escapeshellarg('Merge des revisions '.$this->commit_number_message.' '.$message);
    }


    /**
     * Build the login / password arguments
     *
     * @return  string  The arguments with login / password if there are defined
     * @access  private
     */
    private function buildLogin()
    {
        $login_pass = '';
        $username = $this->project->getUsername();
        $password = $this->project->getPassword();

        if ($username !== null) {
            $login_pass .= ' --username='.escapeshellarg($username);
        }

        if ($password !== null) {
            $login_pass .= ' --password='.escapeshellarg($password);
        }

        return $login_pass;
    }
}