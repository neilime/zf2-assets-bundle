<?php
namespace Neilime\AssetsBundle\Service\Filter;
interface FilterInterface{
	/**
	 * @param string $sContent
	 * @return string
	 */
	public function run($sContent);
}
