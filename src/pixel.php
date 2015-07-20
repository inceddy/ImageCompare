<?php

class Pixel {
	private $x = 0;
	private $y = 0;

	/**
	 * Color of this pixel
	 * @var Color
	 */
	
	private $color = null;

	public function __construct($x, $y, Color $color)
	{
		$this->x = $x;
		$this->y = $y;

		$this->color = $color;
	}

	public function x()
	{
		return $this->x;
	}

	public function y()
	{
		return $this->y;
	}

	public function position()
	{
		return array('x' => $this->x, 'y' => $this->y);
	}

	public function color()
	{
		return $this->color;
	}
}