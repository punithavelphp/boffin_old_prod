<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.2.39
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Service;

use Magento\Search\Model\SynonymAnalyzer;
use Magento\Search\Model\SynonymGroupFactory;
use Magento\Search\Model\SynonymGroupRepository;

class SynonymService
{
    private $synonymRepository;

    private $synonymFactory;

    private $synonymAnalyzer;

    private $cloudService;

    public function __construct(
        SynonymGroupRepository $synonymRepository,
        SynonymGroupFactory    $synonymFactory,
        SynonymAnalyzer        $synonymAnalyzer,
        CloudService           $cloudService
    ) {
        $this->synonymRepository = $synonymRepository;
        $this->synonymFactory    = $synonymFactory;
        $this->synonymAnalyzer   = $synonymAnalyzer;
        $this->cloudService      = $cloudService;
    }

    public function getSynonyms(array $terms, int $storeId): array
    {
        $result = [];
        foreach ($terms as $term) {
            $term     = trim($term);
            $synonyms = $this->synonymAnalyzer->getSynonymsForPhrase($term);
            if (empty($synonyms)) {
                continue;
            }

            foreach (explode(' ', $term) as $key => $word) {
                if (!isset($synonyms[$key])) {
                    continue;
                }

                if (count($synonyms[$key]) > 1) {
                    if (array_search($word, $synonyms[$key]) !== false) {
                        unset($synonyms[$key][array_search($word, $synonyms[$key])]);
                    }
                    $tmp = [$word => $synonyms[$key]];
                } else {
                    $tmp = [$word => ''];
                }

                $result = array_merge($result, $tmp);
            }
        }
        $result = array_filter($result);

        return $result;
    }

    public function import(string $file, array $storeIds): \Generator
    {
        $result = [
            'synonyms' => 0,
            'total'    => 0,
            'errors'   => 0,
            'message'  => '',
        ];

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $file    = explode('/', $file);
            $file    = end($file);
            $content = $this->cloudService->get('search', 'synonym', $file);
        }

        if (!$content) {
            $result['errors']++;
            $result['message'] = __("The file is empty or doesn't exists.");
            yield $result;
            yield $result;
        } else {
            if (strlen($content) > 10000 && php_sapi_name() != "cli") {
                $result['errors']++;
                $result['message'] = __('File is too large. Please use CLI interface (bin/magento mirasvit:search:synonym --file EN.csv --store 1)');
                yield $result;
            } else {
                $synonyms = [];
                foreach (explode(PHP_EOL, $content) as $line) {
                    $synonyms[] = str_getcsv($line);
                }

                if (!is_array($storeIds)) {
                    $storeIds = [$storeIds];
                }

                foreach ($storeIds as $storeId) {
                    $result['total'] = count($synonyms);

                    foreach ($synonyms as $group) {
                        if (!is_array($group) || count($group) <= 1) {
                            $result['errors']++;
                            yield $result;
                        } else {
                            try {
                                $synonymGroup = implode(',', $group);

                                if ($term = $this->validateSynonyms($synonymGroup)) {
                                    $result['errors']++;
                                    $result['message'] = __('The term %1 can\'t use only special symbols.', $term);
                                } else {
                                    $model = $this->synonymFactory->create()
                                        ->setSynonymGroup($synonymGroup)
                                        ->setStoreId(1)
                                        ->setWebsiteId((int)$storeId);

                                    $this->synonymRepository->save($model);

                                    $result['synonyms']++;
                                }
                            } catch (\Exception $e) {
                                $result['errors']++;

                                $result['message'] = $e->getMessage();
                            }
                            yield $result;
                        }
                    }
                }
            }
        }
    }

    public function validateSynonyms(string $synonyms): string
    {
        $words = explode(',', $synonyms ?? '');
        $words = array_map('trim', $words);
        foreach ($words as $word) {
            if ($word) {
                $matches = null;
                preg_match('/[\p{L}\p{N}]/', $word, $matches);
                if (!$matches) {
                    return $word;
                }
            }

        }

        return '';
    }
}
