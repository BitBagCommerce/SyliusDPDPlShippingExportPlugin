<?php

namespace spec\BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\DpdPlShippingExportPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    function let (
        WebClientInterface $webClient,
        RequestStack $requestStack,
        Filesystem $filesystem,
        ObjectManager $objectManager
    ) {
        $shippingLabelsPath = 'labels';
        $this->beConstructedWith(
            $webClient,
            $requestStack,
            $filesystem,
            $objectManager,
            $shippingLabelsPath,
            'PDF',
            'PDF',
            'Test',
        );
    }

    function it_export_shipment
    (
        ResourceControllerEvent $exportShipmentEvent,
        ShippingExportInterface $shippingExport,
        ShippingGatewayInterface $shippingGateway,
        ShipmentInterface $shipment,
        WebClientInterface $webClient,
        Order $order,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
    )
    {
        $webClient->setShippingGateway($shippingGateway);
        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

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

        $this->exportShipment($exportShipmentEvent);
    }
}
