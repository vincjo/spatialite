<?php
use \Spatialite\SPL;
use PHPUnit\Framework\TestCase;

class SPLTest extends TestCase
{
    public function testCreateNewEmptyDB(): void
    {
        $this->assertEquals(
            'test.sqlite',
            SPL::CreateNewEmptyDB('test.sqlite')
        );
    }
}