<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Mocker;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use Sylius\Behat\Service\Mocker\MockerInterface;

class DPDApiMocker
{
    /** @var MockerInterface */
    private $mocker;

    /**
     * DPDApiMocker constructor.
     */
    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function performActionInApiSuccessfulScope(callable $action)
    {
        $this->mockApiSuccessfulDPDResponse();
        $action();
        $this->mocker->unmockAll();
    }

    private function mockApiSuccessfulDPDResponse()
    {
        $createShipmentResult = (object) [
            'createShipmentResult' => (object) [
                'label' => (object) [
                    'labelContent' => 'test',
                    'labelType' => 't',
                ],
            ],
        ];

        $this
            ->mocker
            ->mockService(
                'bitbag.dpd_pl_shipping_export_plugin.api.soap_client',
                SoapClientInterface::class,
            )
            ->shouldReceive('createShipment')
            ->andReturn($createShipmentResult)
        ;
    }
}
