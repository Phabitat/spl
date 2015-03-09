<?php

namespace Spl\Test\Unit\Utility;

use Spl\Test\AbstractTestCase;
use Spl\Utility\StringUtility;

class StringUtilityTestCase extends AbstractTestCase
{

    /**
     * @covers       \Spl\Utility\StringUtility::camelise
     * @dataProvider cameliseProvider
     *
     * @param string $string
     * @param string $newPrefix
     * @param string $oldPrefix
     * @param string $expected
     */
    public function testCamelise($string, $newPrefix, $oldPrefix, $expected)
    {
        $actual = StringUtility::camelise($string, $newPrefix, $oldPrefix);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function cameliseProvider()
    {
        return [
            ['abcdefghijkl', null, null, 'abcdefghijkl'],
            ['ABC def ghI Jkl', null, null, 'abc Def Ghi Jkl'],
            ['ABC def ghI Jkl', '', null, 'abcDefGhiJkl'],
            ['abc  def--ghi__jkl', null, null, 'abc  Def--Ghi__Jkl'],
            ['abc  def--ghi__jkl', ' ', null, 'abc Def Ghi Jkl'],
            ['abc  def--ghi__jkl', ' ', '/.+/', 'abc Def Ghi Jkl'],
            ['ABC_DEF_GHI_JKL', '', '/_+/', 'abcDefGhiJkl']
        ];
    }

    /**
     * @covers       \Spl\Utility\StringUtility::uncamelise
     * @dataProvider uncameliseProvider
     *
     * @param string $string
     * @param string $newPrefix
     * @param string $oldPrefix
     * @param string $expected
     */
    public function testUncamelise($string, $newPrefix, $oldPrefix, $expected)
    {
        $actual = StringUtility::uncamelise($string, $newPrefix, $oldPrefix);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function uncameliseProvider()
    {
        return [
            ['AbcDefGhiJkl', null, null, 'abcdefghijkl'],
            ['AbcDefGhiJkl', '-', null, 'abc-def-ghi-jkl'],
            ['Abc DEF GHi jKL', null, null, 'abc def ghi jkl'],
            ['abc  Def--Ghi__Jkl', null, null, 'abc  def--ghi__jkl'],
            ['abc  Def--Ghi__Jkl', ' ', null, 'abc def ghi jkl'],
            ['abc  Def--Ghi__Jkl', ' ', '/.+/', 'abc def ghi jkl']
        ];
    }

    /**
     * @covers \Spl\Utility\StringUtility::random
     */
    public function testRandom()
    {
        $this->assertRegExp('/^[a-z]{10}$/', StringUtility::random(10, false, true, false, false));
        $this->assertRegExp('/^[^a-z]{10}$/', StringUtility::random(10, true, false, true, true));

        $this->assertRegExp('/^[A-Z]{10}$/', StringUtility::random(10, true, false, false, false));
        $this->assertRegExp('/^[^A-Z]{10}$/', StringUtility::random(10, false, true, true, true));

        $this->assertRegExp('/^[0-9]{10}$/', StringUtility::random(10, false, false, true, false));
        $this->assertRegExp('/^[^0-9]{10}$/', StringUtility::random(10, true, true, false, true));

        $this->assertRegExp('/^[^0-9a-zA-Z]{10}$/', StringUtility::random(10, false, false, false, true));
        $this->assertRegExp('/^[0-9a-zA-Z]{10}$/', StringUtility::random(10, true, true, true, false));

        // Because it's "very" random, lets run it a couple of times to make sure…

        for ($i = 0; $i < 1000; $i++) {
            $this->assertRegExp('/[0-9]/', $string = StringUtility::random(10, true, true, true, true), 'On attempt: ' . $i);
            $this->assertRegExp('/[a-z]/', $string, 'On attempt: ' . $i);
            $this->assertRegExp('/[A-Z]/', $string, 'On attempt: ' . $i);
            $this->assertRegExp('/[^0-9a-zA-Z]/', $string, 'On attempt: ' . $i);
        }
    }
}