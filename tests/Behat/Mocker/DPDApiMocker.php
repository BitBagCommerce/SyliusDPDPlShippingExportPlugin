<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Mocker;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use Sylius\Behat\Service\Mocker\MockerInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
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
