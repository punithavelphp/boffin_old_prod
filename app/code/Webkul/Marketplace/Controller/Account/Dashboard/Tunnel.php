<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Account\Dashboard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result;
use Magento\Framework\Encryption\Helper\Security;
use Webkul\Marketplace\Helper\Dashboard\Data as MpDashboardHelper;
use Webkul\Marketplace\Helper\Data as MpHelper;

/**
 * Webkul Marketplace Account Dashboard Tunnel Controller.
 */
class Tunnel extends Action
{
    /**
     * @var Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var MpDashboardHelper
     */
    protected $mpDashboardHelper;

    /**
     * @var \Magento\Framework\HTTP\ZendClient
     */
    protected $httpZendClient;

    /**
     * @var MpHelper
     */
    protected $mpHelper;
    
    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $decoderInterface;

    /**
     * Construct
     *
     * @param Context $context
     * @param Result\RawFactory $resultRawFactory
     * @param MpDashboardHelper $mpDashboardHelper
     * @param \Magento\Framework\HTTP\ZendClient $httpZendClient
     * @param MpHelper $mpHelper
     * @param \Magento\Framework\Url\DecoderInterface $decoderInterface
     */
    public function __construct(
        Context $context,
        Result\RawFactory $resultRawFactory,
        MpDashboardHelper $mpDashboardHelper,
        \Magento\Framework\HTTP\LaminasClient $httpZendClient,
        MpHelper $mpHelper,
        \Magento\Framework\Url\DecoderInterface $decoderInterface
    ) {
        parent::__construct($context);
        $this->_resultRawFactory = $resultRawFactory;
        $this->mpDashboardHelper = $mpDashboardHelper;
        $this->httpZendClient = $httpZendClient;
        $this->mpHelper = $mpHelper;
        $this->decoderInterface = $decoderInterface;
    }
	
	public function convertGoogleChartToQuickChart($googleChartJson) {
    $data = json_decode($googleChartJson, true);
    if (!$data) return null;

    $chartType = $data['cht'] ?? '';
    $labels = [];
    $values = [];

    // Parse chart data
    if (isset($data['chl'])) {
        $labels = explode('|', $data['chl']);
    }

    if (isset($data['chd'])) {
        $rawData = explode(':', $data['chd'])[1] ?? '';
        $values = array_map('floatval', explode(',', $rawData));
    }

    $colors = isset($data['chco']) ? array_map(function($c) {
        return '#' . $c;
    }, explode('|', $data['chco'])) : [];

    // Decide chart type
    $quickChartType = 'bar';
    $extraOptions = [];

    switch ($chartType) {
        case 'p':
            $quickChartType = 'pie';
            break;
        case 'bhs':
            $quickChartType = 'bar';
            $extraOptions['indexAxis'] = 'y';
            break;
        case 'bvs':
        case 'bvg':
            $quickChartType = 'bar';
            break;
        case 'lc':
        case 'ls':
            $quickChartType = 'line';
            break;
        default:
            // fallback
            $quickChartType = 'bar';
    }

    $chartConfig = [
        'type' => $quickChartType,
        'data' => [
            'labels' => $labels,
            'datasets' => [[
                'label' => $quickChartType === 'pie' ? '' : 'Values',
                'data' => $values,
                'backgroundColor' => $colors,
            ]]
        ],
        'options' => array_merge([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Converted Chart'
                ]
            ],
            'responsive' => true,
        ], $extraOptions)
    ];

    // Chart dimensions
    $width = 500;
    $height = 300;
    if (!empty($data['chs'])) {
        list($width, $height) = explode('x', $data['chs']);
    }

    $quickChartUrl = "https://quickchart.io/chart?width={$width}&height={$height}&c=" . urlencode(json_encode($chartConfig));
    return $quickChartUrl;
}


    /**
     * Request to get seller statistics graph image to the web-service.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        
        $errorMessage = __('invalid request');
        $httpCode = 400;
        $getEncodedParamData = $this->_request->getParam('param_data');
        $getEncryptedHashData = $this->_request->getParam('encrypted_data');
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create();
        if ($getEncodedParamData && $getEncryptedHashData) {
            /** @var $helper \Webkul\Marketplace\Helper\Dashboard\Data */
            $helper = $this->mpDashboardHelper;
			 
            $newEncryptedHashData = $helper->getChartEncryptedHashData($getEncodedParamData);
            if (Security::compareStrings($newEncryptedHashData, $getEncryptedHashData)) {
                $params = null;
                $paramsJson = $this->decoderInterface->decode(urldecode($getEncodedParamData));
     // print_r($paramsJson);
                if ($paramsJson) {
                    $params = json_decode($paramsJson, true);
                }
				

				$data = $params;

// Extract `chm` string (this contains Orders & Sales)
$entries = explode('|', $data['chm']);

$labels = [];
$sales = [];
$orders = [];

foreach ($entries as $entry) {
    if (preg_match('/f(IN-[A-Z]+):Orders-(\d+)\sSales-([\d.]+)/', $entry, $matches)) {
        $region = $matches[1];
        $orderCount = (int)$matches[2];
        $salesValue = (float)$matches[3];

        $labels[] = $region;
        $orders[] = $orderCount;
        $sales[] = $salesValue;
    }
}

// Build QuickChart config
$chartConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Sales (INR)',
                'data' => $sales,
                'backgroundColor' => 'rgba(75, 192, 192, 0.7)'
            ],
            [
                'label' => 'Orders',
                'data' => $orders,
                'backgroundColor' => 'rgba(255, 159, 64, 0.7)'
            ]
        ]
    ],
    'options' => [
        'responsive' => true,
        'plugins' => [
            'title' => [
                'display' => true,
                'text' => 'State-wise Sales & Orders'
            ]
        ],
        'scales' => [
            'y' => ['beginAtZero' => true]
        ]
    ]
];


				
                if ($params) {
                    try {
                        /** @var $httpZendClient \Magento\Framework\HTTP\ZendClient */
                        $httpZendClient = $this->httpZendClient;
                        $response = $httpZendClient->setUri(
							// $this->convertGoogleChartToQuickChart($paramsJson)
                          'https://quickchart.io/chart?c='. urlencode(json_encode($chartConfig))
                        )->setOptions(
                            ['timeout' => 5]
                        )->send(
                        );
                        $responseHeaders = $response->getHeaders();
                        $resultRaw
							//->setHeader('Content-type', $responseHeaders['Content-type'])
                            ->setContents($response->getBody());

                        return $resultRaw;
                    } catch (\Exception $e) {
                        $this->mpHelper->logDataInLogger(
                            "controller_account_dashboard_tunnel execute : ".$e->getMessage()
                        );
                        $errorMessage = __('see error log for details');
                        $httpCode = 503;
                    }
                }
            }
        }
        $resultRaw->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setHttpResponseCode($httpCode)
            ->setContents(__('Service unavailable: %1', $errorMessage));

        return $resultRaw;
    }
}
