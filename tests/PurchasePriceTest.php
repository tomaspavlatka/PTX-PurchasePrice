<?php

class PurchasePriceTest extends PHPUnit_Framework_TestCase {

    protected $PurchasePrice;

    protected function setUp()
    {
        parent::setUp();
        $this->PurchasePrice = new PTX\PurchasePrice();
    }


    protected function tearDown()
    {
        parent::tearDown();
        unset($this->PurchasePrice);
    }

    /**
     * @dataProvider data_testCountProductTotal
     */
    public function testCountProductTotal_Quantity_CorrectTotal($quantity, $expected)
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19);
        $total_price = $this->PurchasePrice->count_product_total($product_data, $quantity);

        // Compare.
        $this->assertEquals($total_price, $expected, $quantity);
    }

    public function testCompleteDiscountInfo_ProductWithFixValidDiscount_CorrectInfo()
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19,
            'discount' => 20, 'discount_type' => 'fix');
        $complete_data = $this->PurchasePrice->complete_discount_info($product_data);

        $expected = array(
            'price' => 79.96,
            'tax_percentage' => 19,
            'discount' => 20,
            'discount_type' => 'fix',
            'discount_value' => 20,
            'discount_percentage' => 25.01);

        $this->assertEquals($complete_data, $expected);
    }

    public function testCompleteDiscountInfo_ProductWithFixInValidDiscount_CorrectInfo()
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19,
            'discount' => 100, 'discount_type' => 'fix');
        $complete_data = $this->PurchasePrice->complete_discount_info($product_data);

        $expected = array(
            'price' => 79.96,
            'tax_percentage' => 19,
            'discount' => 100,
            'discount_type' => 'fix',
            'discount_value' => null,
            'discount_percentage' => null);

        $this->assertEquals($complete_data, $expected);
    }

    public function testCompleteDiscountInfo_ProductWithInValidDiscount_CorrectInfo()
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19,
            'discount' => 100, 'discount_type' => 'fixx');
        $complete_data = $this->PurchasePrice->complete_discount_info($product_data);

        $expected = array(
            'price' => 79.96,
            'tax_percentage' => 19,
            'discount' => 100,
            'discount_type' => 'fixx',
            'discount_value' => null,
            'discount_percentage' => null);

        $this->assertEquals($complete_data, $expected);
    }

    public function testCompleteDiscountInfo_ProductWithPercentageValidDiscount_CorrectInfo()
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19,
            'discount' => 20, 'discount_type' => 'percentage');
        $complete_data = $this->PurchasePrice->complete_discount_info($product_data);

        $expected = array(
            'price' => 79.96,
            'tax_percentage' => 19,
            'discount' => 20,
            'discount_type' => 'percentage',
            'discount_value' => 15.99,
            'discount_percentage' => 20);

        $this->assertEquals($complete_data, $expected);
    }

    public function testCompleteDiscountInfo_ProductWithPercentageInValidDiscount_CorrectInfo()
    {
        $product_data = array(
            'price' => '79.96', 'tax_percentage' => 19,
            'discount' => 100, 'discount_type' => 'percentage');
        $complete_data = $this->PurchasePrice->complete_discount_info($product_data);

        $expected = array(
            'price' => 79.96,
            'tax_percentage' => 19,
            'discount' => 100,
            'discount_type' => 'percentage',
            'discount_value' => null,
            'discount_percentage' => null);

        $this->assertEquals($complete_data, $expected);
    }

    public function testCountTotal_ProductsWithSameTax_CorrectTotal()
    {
        $products_data = array(
            array(
                'price' => '79.96', 'tax_percentage' => 19, 'quantity' => 5),
            array(
                'price' => '67.79', 'tax_percentage' => 19, 'quantity' => 10));
        $total_price = $this->PurchasePrice->count_total($products_data);

        // Expected.
        $expected = array(
            'total' => 1077.7,
            'subtotal' => 905.63,
            'tax' => array(
                19 => 172.07),
            'discount' => 0);

        // Compare.
        $this->assertEquals($total_price, $expected);
    }

    public function testCountTotal_ProductsWithDifferentTaxes_CorrectTotal()
    {
        $products_data = array(
            array(
                'price' => '79.96', 'tax_percentage' => 19, 'quantity' => 5),
            array(
                'price' => '67.79', 'tax_percentage' => 5, 'quantity' => 10));
        $total_price = $this->PurchasePrice->count_total($products_data);

        // Expected.
        $expected = array(
            'total' => 1077.7,
            'subtotal' => 981.59,
            'tax' => array(
                5 => 32.28,
                19 => 63.83),
            'discount' => 0);

        // Compare.
        $this->assertEquals($total_price, $expected);
    }

    public function testCountTotal_ProductsWithDiscount_CorrectTotal()
    {
        $products_data = array(
            array(
                'price' => '79.96', 'tax_percentage' => 19, 'quantity' => 5, 'discount' => 20, 'discount_type' => 'fix'),
            array(
                'price' => '67.79', 'tax_percentage' => 19, 'quantity' => 10, 'discount' => 10, 'discount_type' => 'percentage'));
        $total_price = $this->PurchasePrice->count_total($products_data);

        // Expected.
        $expected = array(
            'total' => 909.9,
            'subtotal' => 764.62,
            'tax' => array(
                19 => 145.28),
            'discount' => 167.8);

        // Compare.
        $this->assertEquals($total_price, $expected);
    }

    public function data_testCountProductTotal()
    {
        return array(
            array(
                1, array('total' => 79.96, 'subtotal' => 67.19, 'tax' => 12.77, 'discount' => 0)
            ),
            array(
                5, array('total' => 399.8, 'subtotal' => 335.97, 'tax' => 63.83, 'discount' => 0)
            ),
            array(
                10, array('total' => 799.60, 'subtotal' => 671.93, 'tax' => 127.67, 'discount' => 0)
            ),
            array(
                50, array('total' => 3998, 'subtotal' => 3359.66, 'tax' => 638.34, 'discount' => 0)
            ),
            array(
                100, array('total' => 7996.0, 'subtotal' => 6719.33, 'tax' => 1276.67, 'discount' => 0)
            ),
            array(
                996, array('total' => 79640.16, 'subtotal' => 66924.50, 'tax' => 12715.66, 'discount' => 0)
            )
        );
    }
}