<?php

class CrawlerOutline {
	private $outline = array();

	/**
	 * Is this outline closed
	 * @var boolean
	 */
	
	private $closed = false;

	public function __construct(Pixel $pixel)
	{
		$this->push($pixel);
	}

	public function size()
	{
		return sizeof($this->outline);
	}

	public function push(Pixel $pixel)
	{
		if (isset($this->outline[0]) && $this->outline[0]->position() == $pixel->position()) {
			return $this->complete();
		}

		$this->outline[] = $pixel;
		return $this;
	}

	private function complete()
	{
		usort($this->outline, function($p1, $p2){
			return $p1->x() == $p2->x() ? ($p1->y() - $p2->y()) : ($p1->x() - $p2->x());
		});

		$this->closed = true;

		return $this;
	}

	public function closed()
	{
		return $this->closed;
	}

	public function contains(Pixel $pixel)
	{
		$x = $pixel->x();
		$y = $pixel->y();

		$bounderies = $this->bounderies();

		return ($x >= $bounderies['x'] && $x <= $bounderies['x'] + $bounderies['width'] && $y >= $bounderies['y'] && $y <= $bounderies['y'] + $bounderies['height']);

		/*

		$top = $bottom = $left = $right = false;

		foreach ($this->outline as $pixel) {
			if ($pixel->x() <= $x && $pixel->y() == $y)
				$left = true;
			if ($pixel->x() >= $x && $pixel->y() == $y)
				$right = true;
			if ($pixel->x() == $x && $pixel->y() <= $y)
				$top = true;
			if ($pixel->x() == $x && $pixel->y() >= $y)
				$top = true;
		}

		return $top && $bottom && $left && $right;

		*/
	}

	public function getColumnSkip(Pixel $pixel)
	{
		$y = $pixel->y();
		$x = $pixel->x();

		$start = null;
		$stop  = null;

		foreach($this->outline as $pixel) {
			// Wrong column
			if ($pixel->x() != $x)
				continue;

			// Standing on outline / start pixel
			if ($pixel->y() == $y) {
				$start = $pixel;
				continue;
			}

			if ($pixel->y() > $y) {
				$stop = $pixel;
				break;
			}
		}



		if ($start === null)
			return 0;

		if ($stop === null)
			return 1;

		return $stop->y() - $start->y();
	}

	public function bounderies()
	{
		$bounderies = array(null, null, null, null);

		foreach($this->outline as $pixel) {
			// Min X & Y
			if ($bounderies[0] === null || $pixel->x() < $bounderies[0])
				$bounderies[0] = $pixel->x();
			if ($bounderies[1] === null || $pixel->y() < $bounderies[1])
				$bounderies[1] = $pixel->y();

			// Max X & Y
			if ($bounderies[2] === null || $pixel->x() > $bounderies[2])
				$bounderies[2] = $pixel->x();
			if ($bounderies[3] === null || $pixel->y() > $bounderies[3])
				$bounderies[3] = $pixel->y();
			
		}

		return array('x' => $bounderies[0], 'y' => $bounderies[1], 'width' => $bounderies[2] - $bounderies[0], 'height' => $bounderies[3] - $bounderies[1]);
	}
}