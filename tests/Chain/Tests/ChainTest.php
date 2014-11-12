<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Chain\Tests;

use Chain\Chain;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ChainTest extends \PHPUnit_Framework_TestCase
{

    public function processProvider()
    {
        return array(
            array(
                new Process('ls ~'),
                array(),
                'ls ~'
            ),
            array(
                new Process('ls ~'),
                array(
                    '|'  => 'sort',
                    '&&' => 'pwgen',
                    ';' => new Process('cat ~'),
                    '1>' => '/dev/null',

                ),
                'ls ~ | sort && pwgen ; cat ~ 1> /dev/null'
            )
        );
    }

    /**
     * @dataProvider processProvider
     */
    public function testGetProcess($process, $processes, $expected)
    {
        $chain = new Chain($process);

        foreach ($processes as $link => $process) {
            $chain->add($link, $process);
        }

        $this->assertEquals(
            $expected,
            $chain->getProcess()->getCommandLine()
        );
    }
}
