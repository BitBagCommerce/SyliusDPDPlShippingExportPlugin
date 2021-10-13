<?php

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Mocker;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use Sylius\Behat\Service\Mocker\MockerInterface;

class DPDApiMocker
{
    /**
     * @var MockerInterface
     */
    private $mocker;

    /**
     * DPDApiMocker constructor.
     * @param MockerInterface $mocker
     */
    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    /**
     * @param callable $action
     */
    public function performActionInApiSuccessfulScope(callable $action)
    {
        $this->mockApiSuccessfulDPDResponse();
        $action();
        $this->mocker->unmockAll();

    }

    private function mockApiSuccessfulDPDResponse()
    {
        $createShipmentResult = (object)[
            'createShipmentResult' => (object)[
                'label' => (object)[
                    'labelContent' => 'test',
                    'labelType' => 't'
                ]
            ],
        ];

        $this
            ->mocker
            ->mockService(
                'bitbag.dpd_pl_shipping_export_plugin.api.soap_client',
                SoapClientInterface::class
            )
            ->shouldReceive('createShipment')
            ->andReturn($createShipmentResult)
        ;
    }
}
