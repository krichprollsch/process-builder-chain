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

    public function append($process)
    {
      return $this->add('>>', $process);
    }

    public function pipe($process)
    {
        return $this->add('|', $process);
    }

    public function orDo($process)
    {
        return $this->add('||', $process);
    }

    public function andDo($process)
    {
        return $this->add('&&', $process);
    }

    public function afterDo($process)
    {
        return $this->add(';', $process);
    }

    public function input($process)
    {
        return $this->add('<', $process);
    }

    public function output($process)
    {
        return $this->add('>', $process);
    }

    public function errors($process)
    {
        return $this->add('2>', $process);
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

        return $this;
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
