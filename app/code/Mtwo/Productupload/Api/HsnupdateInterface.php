<?php
namespace Mtwo\Productupload\Api;

interface HsnupdateInterface
{
	
    /**
     * Return 
     *
     * @param int $productId
	 * @param string $hsncode
      * @return mixed $result
     */
    public function updateHsncode($productId,$hsncode);
	
}
