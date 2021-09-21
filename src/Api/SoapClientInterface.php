<?php

declare(strict_types=1);

namespace BitBag\DpdPlShippingExportPlugin\Api;

interface SoapClientInterface
{
    public function createShipment(array $requestData, $wsdl);
}
