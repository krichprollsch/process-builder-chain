<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chain;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\ProcessBuilder;

class Chain
{
    protected $chain;
    protected $process;

    protected $links = array(null, ';', '|', '||', '&&', '&', '>', '>>', '2>', '1>', '<');

    public function __construct($process)
    {
        $this->process = $this->prepareProcess($process, $strict = true);
        $this->add(null, $process);
    }

    public function add($link, $process)
    {
        if (!in_array($link, $this->links)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'link must be in %s (%s given)',
                    implode(' ', $this->links),
                    $link
                )
            );
        }

        if (!is_null($link)) {
            $this->chain[] = $link;
        }

        $process = $this->prepareProcess($process);
        $this->chain[] = is_string($process) ? $process : $process->getCommandLine();
    }

    private function prepareProcess($process, $strict = false)
    {
        if ($process instanceof ProcessBuilder) {
            return $process->getProcess();
        }

        if ($process instanceof Process) {
            return $process;
        }

        if (!$strict) {
            return escapeshellcmd((string) $process);
        }

        throw new \InvalidArgumentException(
            'process must be ProcessBuilder|Process'
        );
    }

    public function getProcess()
    {
        $process = clone $this->process;
        $process->setCommandLine(
            implode(' ', $this->chain)
        );

        return $process;
    }
}
