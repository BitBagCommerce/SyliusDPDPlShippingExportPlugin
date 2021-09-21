<?php

declare(strict_types=1);

namespace BitBag\DpdPlShippingExportPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface WebClientInterface
{
    public const GUARANTEE_TIME0930 = 'TIME0930';

    public const GUARANTEE_TIME1200 = 'TIME1200';

    public const GUARANTEE_SATURDAY = 'SATURDAY';

    /**
     * @return mixed
     */
    public function setShippingGateway(ShippingGatewayInterface $shippingGateway);

    /**
     * @return mixed
     */
    public function setShipment(ShipmentInterface $shipment);
}
