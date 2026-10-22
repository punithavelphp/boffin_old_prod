<?php
namespace Mtwo\ApproveCustomer\Api;

interface FacultiesInterface
{

    /**
     * Return States details
	 * @param string $institute_id
     * @return mixed $result
     */
    public function getFaculties($institute_id);
}
