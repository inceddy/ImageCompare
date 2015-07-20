<?php

class Crawler {
	/**
	 * Walking and lookout directions of the crawler.
	 * @var array
	 */
	
	private $dirs =  array(
		array('x2' =>  1, 'y2' =>  0, 'x1' =>  1, 'y1' => -1, 'x3' =>  1, 'y3' =>  1),
		array('x2' =>  0, 'y2' =>  1, 'x1' =>  1, 'y1' =>  1, 'x3' => -1, 'y3' =>  1),
		array('x2' => -1, 'y2' =>  0, 'x1' => -1, 'y1' =>  1, 'x3' => -1, 'y3' => -1),
		array('x2' =>  0, 'y2' => -1, 'x1' => -1, 'y1' => -1, 'x3' =>  1, 'y3' => -1)
	);

	/**
	 * The current crawling x-position
	 * @var integer
	 */
	
	private $x = 0;


	/**
	 * The current crawling y-position
	 * @var integer
	 */

	private $y = 0;

	public function __construct(ImagePixelMatrix $matrix)
	{
		$this->matrix = $matrix;
	}

	public function crawl($x, $y)
	{
		// Set the starting position to crawl;
		$this->x = $x;
		$this->y = $y;

		$turns = 0;
		$outline = new CrawlerOutline($this->matrix->pixel($x, $y));

		do {
			switch (true) {
				// L1
				case $this->look(1):
					$turns = 0;
					$this->walk()->turnLeft()->walk();
					$outline->push($this->matrix->pixel($this->x, $this->y));
					break;

				// L2
				case $this->look(2):
					$turns = 0;
					$this->walk();
					$outline->push($this->matrix->pixel($this->x, $this->y));
					break;

				// L3
				case $this->look(3):
					$turns = 0;
					$this->turnRight()->walk()->turnLeft()->walk();
					$outline->push($this->matrix->pixel($this->x, $this->y));
					break;

				// Turn
				default:
					$turns++;
					$this->turnRight();
			}

			// Complete condition
			if ($turns == 4 || $outline->closed()) {
				break;
			}

		} while(true);

		return $outline;
	}


	/**
	 * Returns the current position as x-y-array.
	 *
	 * @return array  the current position.
	 * 
	 */
	
	private function position()
	{
		return array('x' => $this->x, 'y' => $this->y);
	}

	/**
	 * Looks at ahead pixel and return if it's white/outside or black 
	 *
	 * @param  integer $pos  the position of the ahead pixel (1,2,3)
	 *
	 * @return boolean  
	 *      
	 */
	
	private function look($pos = 1)
	{
		$x = $this->x + $this->dir('x' . $pos);
		$y = $this->y + $this->dir('y' . $pos);

		$pixel = $this->matrix->pixel($x, $y);

		// Position is outside the matrix
		if ($pixel === null) {
			return false;
		}

		return !$pixel->color()->compare(Color::white(), 5); // Use 5% white tolerance
	}

	public function turnLeft()
	{
		array_unshift($this->dirs, array_pop($this->dirs));
		return $this;
	}

	public function turnRight()
	{
		array_push($this->dirs, array_shift($this->dirs));
		return $this;
	}

	public function walk() {
		$this->x += $this->dir('x2');
		$this->y += $this->dir('y2');
		return $this;
	}

	private function dir($pos)
	{
		return $this->dirs[0][$pos];
	}

	public function reset($x = 0, $y = 0)
	{
		$this->x = $x;
		$this->y = $y;
	}
}