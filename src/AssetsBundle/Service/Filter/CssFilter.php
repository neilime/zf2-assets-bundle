<?php
namespace AssetsBundle\Service\Filter;
class CssFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	/**
	 * @var \CSSmin
	 */
	protected $cssMin;

	/**
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @param string $sContent
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \InvalidArgumentException('Content is not a string : '.gettype($sContent));
		return $this->getCSSmin()->run($sContent);
	}
	
	/**
	 * @throws \InvalidArgumentException
	 * @return \CSSmin
	 */
	protected function getCSSmin(){
		if($this->cssMin instanceof \CSSmin)return $this->cssMin;
		if(!class_exists('CSSmin'))throw new \LogicException('"CSSmin" class does not exist');
		return $this->cssMin = new \CSSmin();
	}
}