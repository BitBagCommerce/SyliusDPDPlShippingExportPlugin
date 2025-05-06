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
use Mockery;

class DPDApiMocker
{
    private $mockedSoapClient;

    public function __construct(SoapClientInterface $soapClient)
    {
        $this->mockedSoapClient = Mockery::mock($soapClient);
    }

    public function performActionInApiSuccessfulScope(callable $action): void
    {
        $this->mockApiSuccessfulDPDResponse();
        $action();
        $this->resetMocks();
    }

    private function mockApiSuccessfulDPDResponse(): void
    {
        $createShipmentResult = (object)[
            'createShipmentResult' => (object)[
                'label' => (object)[
                    'labelContent' => 'test',
                    'labelType' => 't',
                ],
            ],
        ];

        $this->mockedSoapClient
            ->shouldReceive('createShipment')
            ->once()
            ->andReturn($createShipmentResult);
    }

    private function resetMocks(): void
    {
        Mockery::close();
    }
}
