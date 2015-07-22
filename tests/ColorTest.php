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
     * @covers Color::difference
     */
    public function testDifference()
    {
        $this->assertEquals( 1, $this->white->difference($this->black));
        $this->assertEquals(-1, $this->black->difference($this->white));
        $this->assertEquals( 0, $this->black->difference($this->black));
    }

    /**
     * @covers Color::deviation
     */
    public function testDeviation()
    {
        // Black and white
        $this->assertEquals(1, $this->white->deviation($this->black));
        $this->assertEquals(0, $this->black->deviation($this->black));

        // Colorchannels        
        $this->assertEquals(1, $this->blue->deviation($this->red, 'r'));
        $this->assertEquals(0, $this->blue->deviation($this->blue, 'r'));
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

    /**
     * @covers Color::toInt
     */
    public function testToInt()
    {
        $this->assertEquals(0xFFFFFF, $this->white->toInt());
        $this->assertEquals(0x000000, $this->black->toInt());

        $this->assertEquals(0xFF0000, $this->red->toInt());
        $this->assertEquals(0x00FF00, $this->green->toInt());
        $this->assertEquals(0x0000FF, $this->blue->toInt());
    }

    /**
     * @covers Color::toRgb
     */
    public function testToRgb()
    {
        $this->assertEquals([255,255,255], $this->white->toRgb());
    }

    public function testMix()
    {
        $grey = $this->white->mix($this->black);

        $this->assertNotSame($grey, $this->white);

        // Round(255/2) => 128
        $this->assertEquals([128,128,128], $grey->toRgb());

        // Mixing with the same color does not change the result
        $this->assertEquals($grey->toInt(), $grey->mix($grey)->toInt());
    }
    

    public function tearDown()
    {
        // your code here
    }
}
