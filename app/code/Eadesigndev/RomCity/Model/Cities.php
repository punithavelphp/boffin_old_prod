<?php

declare(strict_types=1);

namespace Eadesigndev\RomCity\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;

class Cities implements ConfigProviderInterface
{
    /** @var RomCityRepository  */
    private $romCityRepository;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteria;

    /** @var SerializerInterface  */
    private $serializer;

    public function __construct(
        RomCityRepository $romCityRepository,
        SearchCriteriaBuilder $searchCriteria,
        SerializerInterface $serializer
    ) {
        $this->romCityRepository = $romCityRepository;
        $this->searchCriteria = $searchCriteria;
        $this->serializer = $serializer;
    }

    public function getConfig(): array
    {
        return [
            'cities' => $this->getCities()
        ];
    }

    private function getCities(): string
    {
        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->romCityRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        $return = [];

        /** @var RomCity $item */
        foreach ($items as $item) {
            $return[$item->getRegionId()][] = $item->getCityName();
        }

        return $this->serializer->serialize($return);
    }
	 
	public function getCitiesData() 
    {
        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->romCityRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        $return = [];

        /** @var RomCity $item */
        foreach ($items as $item) {
            $return[$item->getRegionId()][strtolower($item->getCityName())] = $item->getCityName();
        }

        return $return;
    }
	
	public function getCityArray() 
    {
        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->romCityRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        $return = [];

        /** @var RomCity $item */
        foreach ($items as $item) {
            $return[$item->getRegionId()][($item->getId())] = $item->getCityName();
        }

        return $return;
    }
}
