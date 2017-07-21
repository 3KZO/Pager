<?php

namespace Pager;

/**
 *	@author 3kZO
 */
class Pager
{
	
	/**
	 *	@var int
	 */
	private $items;
	
	/**
	 *	@var int
	 */
	private $items_per_page;
	
	/**
	 *	@var callback
	 */
	private $route_generator;
	
	/**
	 *	@var int
	 */
	private $page;
	
	public function __construct($items, $items_per_page, \Closure $route_generator) {
		
		$this->items = $items;
		$this->items_per_page = $items_per_page;
		$this->route_generator = $route_generator;
		
	}
	
	public function setPage($page) {
		$this->page = $page;
	}
	
	public function getPage() {
		
		return preg_match('/^last$/i', $this->page) ? $this->getTotalPages() : max(1, min($this->getTotalPages(), intval($this->page)));
		
	}
	
	public function getTotalPages() {
		return @ceil($this->items / $this->items_per_page);
	}
	
	public function getOffset() {
		return ($this->getPage() - 1) * $this->items_per_page;
	}
	
	public function isOutOfBounds($page) {
		return $page < 1 || $page > $this->getTotalPages();
	}
	
	public function generateRoute($page) {
		return @call_user_func($this->route_generator, $page);
	}
	
	public function hasPrevPage() {
		return 1 < $this->getPage();
	}
	
	public function getPrevPage() {
		return $this->hasPrevPage() ? $this->getPage() - 1 : 1;
	}
	
	public function hasNextPage() {
		return $this->getTotalPages() > $this->getPage();
	}
	
	public function getNextPage() {
		return $this->hasNextPage() ? $this->getPage() + 1 : $this->getTotalPages();
	}
	
	public function render() {
		
		$output = [];
		$output[] = '<div class="pager"><ul>';
		
		if ($this->hasPrevPage()) {
		//	$output[] = '<a href="' . $this->generateRoute($this->getPrevPage()) . '">&lt;Пред.</a>';
		}
		
		if (!$this->isOutOfBounds($this->getPage()-3)) {
			$output[] = '<li><a href="' . $this->generateRoute(1) . '">1</a></li><li><span>...</span></li> ';
		}
		
		for ($i = $this->getPage()-2;
			 $i <=$this->getPage()+2;
			 $i++) {
			if (!$this->isOutOfBounds($i)) {
				$output[] = $this->getPage() == $i
								? '<li class="current_page"><span>' . $i . '</span></li>'
								: '<li><a href="' . $this->generateRoute($i) . '">' . $i . '</a></li>';
			}
		}
		
		if (!$this->isOutOfBounds($this->getPage()+3)) {
			$output[] = '<li><span>...</span></li><li><a href="' . $this->generateRoute($this->getTotalPages()) . '">' . $this->getTotalPages() . '</a></li>';
		}
		
		if ($this->hasNextPage()) {
		//	$output[] = '<a href="' . $this->generateRoute($this->getNextPage()) . '">След.&gt;</a>';
		}
		
		$output[] = '</ul></div>';
		
		return implode($output);
		
	}
	
}