<?php

namespace PTX;

class PurchasePrice {

    public function __construct()
    {
    }

    /**
     * Counts total amount for a product.
     *
     * @param array $product_data - data about product
     * @param int $quantity - quantity
     *
     * @return array price data.
     */
    public function count_product_total($product_data, $quantity) {
        // 1. Extract some needed variables.
        $tax_percentage = $product_data['tax_percentage'];

        // 2. Complete data for discount.
        $product_data = $this->complete_discount_info($product_data);

        // 3. Let's find default price.
        $discount_total = 0;
        $price = $product_data['price'];
        if(!empty($product_data['discount_value'])) {
            $discount_value = $product_data['discount_value'];

            $price -= $discount_value;
            $discount_total = $discount_value * $quantity;
        }

        // 4. Let's count prices.
        $total = round($price * $quantity, 2);
        $subtotal = round($total / (1 + ($tax_percentage / 100)), 2);
        $tax_value = $total - $subtotal;

        return array(
            'total' => $total,
            'subtotal' => $subtotal,
            'tax' => $tax_value,
            'discount' => $discount_total);
    }

    /**
     * Counts total for all products.
     *
     * @param array $products_data - array with info for all products.
     *
     * @return array with price info.
     */
    public function count_total($products_data){
        $total_price = array(
            'total' => 0, 'subtotal' => 0, 'tax' => array(), 'discount' => 0);

        foreach($products_data as $product_data) {
            // Count total price foreach product.
            $product_total = $this->count_product_total($product_data, $product_data['quantity']);

            // Update total + subtotal.
            $total_price['total'] += $product_total['total'];
            $total_price['subtotal'] += $product_total['subtotal'];
            $total_price['discount'] += $product_total['discount'];

            // Update info about taxes.
            $tax = $product_data['tax_percentage'];
            if(!array_key_exists($tax, $total_price['tax'])) { // If we do not have this tax as key => create it.
                $total_price['tax'][$tax] = 0;
            }
            $total_price['tax'][$tax] += $product_total['tax'];
        }

        return (array)$total_price;
    }

    /**
     * Completes info about discount.
     *
     * @param array $product_data - data about product.
     *
     * @return array
     */
    public function complete_discount_info($product_data)
    {
        // Find discount.
        $price = $product_data['price'];
        $discount_value = $discount_percentage = null;

        // If we have info about discount.
        if (array_key_exists('discount', $product_data) && array_key_exists('discount_type', $product_data)) {
            // Extract needed info into variables.
            $discount = $product_data['discount'];
            $discount_type = $product_data['discount_type'];

            // If we have correct discount type and discount.
            if(is_numeric($discount)) {
                if($discount_type == 'fix') {
                    // If discount type is fix, discount must be lower than price.
                    if ($discount < $price) {
                        $discount_value = $discount;
                        $discount_percentage = round($discount / $price * 100, 2);
                    }
                } else if($discount_type == 'percentage') {
                    if ($discount > 0 && $discount < 100) { // If type is percentage, discount must be between 0 and 100 (exl)
                        $discount_value = round($price * $discount / 100, 2);
                        $discount_percentage = $discount;
                    }
                }
            }
        }

        // Complete array.
        $product_data['discount_value'] = $discount_value;
        $product_data['discount_percentage'] = $discount_percentage;

        // Return it back.
        return (array)$product_data;
    }
}