<?php 
namespace Mtwo\ProductAttributes\Model;

class Citylist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $city;
    
    /**
     * 
     * @param \Ves\Brand\Model\Brand $brand
     */
    public function __construct(
        \Eadesigndev\RomCity\Model\CustomCity $city
        ) {
        $this->city = $city;
    }
    
    
    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $cities = $this->city->getCollection();
        $listCity = array();
        foreach ($cities as $city) {
            $listCity[] = array('label' => $city->getCity(),
                'value' => $city->getId());
        }
        return $listCity;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}