<?php

namespace BitBag\DpdPlShippingExportPlugin\Api;

interface SoapClientInterface
{
    public function createShipment(array $requestData, $wsdl);
}
