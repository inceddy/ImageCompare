<?php

class Color {
	const MAX_DIFF = 441.67295593006;
	const MAX_INT  = 16777215;

	private $r = 0;
	private $g = 0;
	private $b = 0;

	public function __construct($r = 0, $g = 0, $b = 0)
	{
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}

	public function r()
	{
		return $this->r;
	}

	public function g()
	{
		return $this->g;
	}

	public function b()
	{
		return $this->b;
	}

	public function toInt()
	{
		return $this->r << 16 + $this->g << 8 + $this->b;
	}


	public function toRgb()
	{
		return [$this->r, $this->g, $this->b];	
	}


	/**
	 * Mixes this color with a given one and returns the new mixed color
	 *
	 * @param  Color  $color the color to mix with
	 *
	 * @return Color         the new mixed color
	 * 
	 */
	
	public function mix(Color $color)
	{
		return new self(
			round(($this->r + $color->r()) / 2),
			round(($this->g + $color->g()) / 2),
			round(($this->b + $color->b()) / 2)
		);
	}


	/**
	 * Calculates the difference between this and a given color
	 *
	 * @param  Color  $color the color to compare with
	 *
	 * @return float         the difference between this and the given color as float between 0 and 1
	 * 
	 */
	
	public function diff(Color $color)
	{
		list($r1, $g1, $b1) = $this->toRgb();
		list($r2, $g2, $b2) = $color->toRgb();

		return sqrt(pow($r1 - $r2, 2) + pow($g1 - $g2, 2) + pow($b1 - $b2, 2)) / self::MAX_DIFF;
	}


	/**
	 * Compares this color to a given one within a tolerance range
	 *
	 * @param  Color   $color     the color to compare with
	 * @param  integer $tolerance the tolerance in percent in which the colors are assumed equal
	 *
	 * @return bool               the eqaulity as boolean
	 * 
	 */
	
	public function compare(Color $color, $tolerance = 0)
	{
		$tolerance /= 100;
		return  $this->diff($color) <= $tolerance;
	}

	public static function fromInt($int) 
	{
		if (!is_int($int) || $int > self::MAX_INT || $int < 0) {
			throw new Exception("Invalid argument for Color::fromInt. Must be an integer between 0 and 16777215");
		}
			
		return new self($int >> 16, $int >> 8 & 255, $int & 255);
	}
}