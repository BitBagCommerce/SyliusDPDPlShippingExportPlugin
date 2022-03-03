<?php

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\NotificationType;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Behaviour\ContainsError;
use Tests\BitBag\DpdPlShippingExportPlugin\Behat\Page\Admin\ShippingGateway\CreatePageInterface;
use Webmozart\Assert\Assert;
use Tests\BitBag\DpdPlShippingExportPlugin\Behat\Page\Admin\ShippingGateway\CreatePage;

final class ShippingGatewayContext implements Context
{
    /**
     * @var CreatePage
     */
    private $createPage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePage $createPage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePage $createPage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    )
    {
        $this->createPage = $createPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I visit the create shipping gateway configuration page for :code
     */
    public function iVisitTheCreateShippingGatewayConfigurationPage($code)
    {
        $this->createPage->open(['code' => $code]);
    }

    /**
     * @When I select the :name shipping method
     */
    public function iSelectTheShippingMethod($name)
    {
        $this->resolveCurrentPage()->selectShippingMethod($name);
    }

    /**
     * @When I fill the :field field with :value
     */
    public function iFillTheFieldWith($field, $value)
    {
        $this->resolveCurrentPage()->fillField($field, $value);
    }

    /**
     * @When I clear the :field field
     */
    public function iClearTheField($field)
    {
        $this->resolveCurrentPage()->fillField($field, "");
    }

    /**
     * @When I add it
     * @When I save it
     */
    public function iTryToAddIt()
    {
        $this->resolveCurrentPage()->submit();
    }

    /**
     * @Then I should be notified that the shipping gateway has been created
     * @Then I should be notified that the shipping gateway has been updated
     */
    public function iShouldBeNotifiedThatTheShippingGatewayWasCreated()
    {
        $this->notificationChecker->checkNotification(
            "Shipping gateway has been successfully",
            NotificationType::success()
        );
    }

    /**
     * @Then :message error message should be displayed
     */
    public function errorMessageForFieldShouldBeDisplayed($message)
    {
        Assert::true($this->resolveCurrentPage()->hasError($message));
    }

    /**
     * @When I fill the :field select option with :option
     */
    public function iFillTheSelectOptionWith($filed, $option)
    {
        $this->resolveCurrentPage()->selectFieldOption($filed, $option);
    }

    private function resolveCurrentPage(): CreatePage
    {
        /** @var CreatePage $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);

        return $page;
    }
}
