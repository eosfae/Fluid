<?php
namespace TYPO3Fluid\Fluid\Tests\Unit\Core\Cache;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Cache\FluidCacheWarmupResult;
use TYPO3Fluid\Fluid\Tests\UnitTestCase;

/**
 * Class FluidCacheWarmupResultTest
 */
class FluidCacheWarmupResultTest extends UnitTestCase
{

    /**
     * @param array $results
     * @param array $expected
     * @dataProvider getCacheWarmupResultTestValues
     * @test
     */
    public function testMerge(array $results, array $expected)
    {
        $result1 = $this->getAccessibleMock(FluidCacheWarmupResult::class, ['dummy']);
        $result1->_set('results', array_pop($results));
        $result2 = $this->getAccessibleMock(FluidCacheWarmupResult::class, ['dummy']);
        $result2->_set('results', array_pop($results));
        $result1->merge($result2);
        $this->assertAttributeSame($expected, 'results', $result1);
    }

    /**
     * @return array
     */
    public function getCacheWarmupResultTestValues()
    {
        return [
            [[['foo' => 'bar'], ['baz' => 'oof']], ['baz' => 'oof', 'foo' => 'bar']],
            [[['foo' => 'bar'], ['baz' => 'oof', 'foo' => 'baz']], ['baz' => 'oof', 'foo' => 'baz']],
        ];
    }

    /**
     * @test
     */
    public function testGetResults()
    {
        $subject = $this->getAccessibleMock(FluidCacheWarmupResult::class, ['dummy']);
        $subject->_set('results', ['foo' => 'bar']);
        $this->assertAttributeEquals(['foo' => 'bar'], 'results', $subject);
    }
}
