<?php

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Page\Admin\ShippingGateway;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function selectShippingMethod($name);

    /**
     * @param string $field
     * @param string $option
     */
    public function selectFieldOption($field, $option);

    /**
     * @param string $field
     * @param string $value
     */
    public function fillField($field, $value);

    public function submit();
}
