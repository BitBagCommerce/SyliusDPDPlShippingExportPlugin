@managing_shipping_gateway_dpd
Feature: Creating shipping gateway
    In order to export shipping data to external shipping provider service
    As an Administrator
    I want to be able to add new shipping gateway with shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And the store has "DPD Express" shipping method with "$10.00" fee

    @ui
    Scenario: Creating DPD Express shipping gateway
        When I visit the create shipping gateway configuration page for "dpd_pl"
        And I select the "DPD Express" shipping method
        And I fill the "WSDL" field with "https://sandbox.dhl24.com.pl/webapi2"
        And I fill the "Username" field with "123"
        And I fill the "Password" field with "123"
        And I fill the "Client number" field with "1204663"
        And I fill the "Name (first and last name or company name)" field with "Ja"
        And I fill the "Postal Code" field with "00001"
        And I fill the "City" field with "Wawa"
        And I fill the "Phone number" field with "123456789"
        And I fill the "COD payment method code" field with "stripe_checkout"
        And I add it
        Then I should be notified that the shipping gateway has been created
