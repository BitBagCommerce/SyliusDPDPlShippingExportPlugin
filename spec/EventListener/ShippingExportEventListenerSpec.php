<?php

namespace spec\BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\DpdPlShippingExportPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    function let (
        WebClientInterface $webClient,
        FlashBagInterface $flashBag,
        Filesystem $filesystem,
        ObjectManager $objectManager
    ) {
        $shippingLabelsPath = 'labels';
        $this->beConstructedWith($webClient, $flashBag, $filesystem, $objectManager, $shippingLabelsPath);
    }

    function it_export_shipment
    (
        ResourceControllerEvent $exportShipmentEvent,
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
        $shippingGateway->getConfigValue('id')->willReturn(1);
        $shippingGateway->getConfigValue('login')->willReturn('test');
        $shippingGateway->getConfigValue('password')->willReturn('test');

        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
        $webClient->setShipment($shipment)->shouldBeCalled();

        $shippingExport->getShipment()->willReturn($shipment);

        $exportShipmentEvent->getSubject()->willReturn($shippingExport);

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
