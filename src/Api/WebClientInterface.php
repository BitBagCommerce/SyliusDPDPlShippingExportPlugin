<?php

namespace BitBag\DpdPlShippingExportPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface WebClientInterface
{
    const GUARANTEE_TIME0930 = 'TIME0930';
    const GUARANTEE_TIME1200 = 'TIME1200';
    const GUARANTEE_SATURDAY = 'SATURDAY';

    /**
     * @param ShippingGatewayInterface $shippingGateway
     *
     * @return mixed
     */
    public function setShippingGateway(ShippingGatewayInterface $shippingGateway);

    /**
     * @param ShipmentInterface $shipment
     *
     * @return mixed
     */
    public function setShipment(ShipmentInterface $shipment);

}
