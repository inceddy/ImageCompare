<?php

class ColorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->red   = Color::fromInt(0xFF0000);
        $this->green = Color::fromInt(0x00FF00);
        $this->blue  = Color::fromInt(0x0000FF);

        $this->white = Color::fromInt(0xFFFFFF);
        $this->black = Color::fromInt(0x000000);
    }

    /**
     * @covers Color::mix
     */
    public function testValueObject()
    {
        $mixedColor = $this->red->mix($this->blue);
        $this->assertTrue($this->blue !== $mixedColor && $this->red !== $mixedColor);
    }

    /**
     * @covers Color::diff
     */
    public function testDiff()
    {
        $this->assertEquals(1, $this->black->diff($this->white));
    }

    /**
     * @covers Color::compare
     */
    public function testCompare()
    {
        // Equal
        $this->assertTrue($this->blue->compare($this->blue));

        // Unequal
        $this->assertTrue(!$this->blue->compare($this->red));
    }

    /**
     * @covers Color::fromInt
     * @expectedException     Exception
     */
    
    public function testFactory()
    {
        Color::fromInt(17000000);
    }

    public function testToInt()
    {
        $this->assertEquals(0xFFFFFF, $this->white->toInt());
    }

    public function testToRgb()
    {
        $this->assertEquals([255,255,255], $this->white->toRgb());
    }
    

    public function tearDown()
    {
        // your code here
    }
}
