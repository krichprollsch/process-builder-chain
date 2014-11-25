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

    public function testAliases()
    {
        $chain = new Chain(new Process('cat'));
        $chain->input('input.txt');
        $chain->pipe('sort');
        $chain->andDo('pwgen');
        $chain->output('result.log');
        $chain->errors('/dev/null');

        $this->assertEquals(
            'cat < input.txt | sort && pwgen > result.log 2> /dev/null',
            $chain->getProcess()->getCommandLine()
        );
    }

    public function testFluidInterface()
    {
        $chain = new Chain(new Process('cat'));
        $chain
            ->input('input.txt')
            ->pipe('sort')
            ->andDo('pwgen')
            ->output('result.log')
            ->errors('/dev/null');

        $this->assertEquals(
            'cat < input.txt | sort && pwgen > result.log 2> /dev/null',
            $chain->getProcess()->getCommandLine()
        );
    }
}
