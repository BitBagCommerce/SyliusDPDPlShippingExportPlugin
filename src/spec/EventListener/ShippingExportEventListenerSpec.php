<?php

namespace spec\BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\DpdPlShippingExportPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    function let(WebClientInterface $webClient, SoapClientInterface $soapClient)
    {
        $this->beConstructedWith($webClient, $soapClient);
    }

    function it_export_shipment
    (
        ExportShipmentEvent $exportShipmentEvent,
        ShippingExportInterface $shippingExport,
        ShippingGatewayInterface $shippingGateway,
        ShipmentInterface $shipment,
        WebClientInterface $webClient,
        SoapClientInterface $soapClient,
        Order $order
    )
    {
        $webClient->setShippingGateway($shippingGateway);

        $shippingGateway->getCode()->willReturn(ShippingExportEventListener::DPD_GATEWAY_CODE);
        $shippingGateway->getConfigValue('wsdl')->willReturn('wsdl');

        $webClient->getRequestData()->willReturn([]);
        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
        $webClient->setShipment($shipment)->shouldBeCalled();

        $shippingExport->getShipment()->willReturn($shipment);

        $exportShipmentEvent->getShippingExport()->willReturn($shippingExport);
        $exportShipmentEvent->addSuccessFlash()->shouldBeCalled();
        $exportShipmentEvent->exportShipment()->shouldBeCalled();
        $exportShipmentEvent->saveShippingLabel('', 'pdf')->shouldBeCalled();
        $shippingExport->getShippingGateway()->willReturn($shippingGateway);

        $order->getNumber()->willReturn(1000);
        $shipment->getOrder()->willReturn($order);

        $soapClient->createShipment([], 'wsdl')->willReturn(
            (object)['createShipmentResult' =>
                (object)['label' =>
                    (object)[
                        'labelContent' => '',
                        'labelType' => 't'
                    ]
                ]
            ]
        );

        $this->exportShipment($exportShipmentEvent);
    }
}
