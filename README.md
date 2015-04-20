# Purchase Price

Simple PHP class which counts final prices for a purchase and its product. It counts with VAT and with discount properly.

## Example

```
<?php

require_once './vendor/autoload.php';


products_data = array(
    array(
        'price'          => '79.96',
        'tax_percentage' => 19,
        'quantity'       => 5,
        'discount'       => 20,
        'discount_type'  => 'fix'),
    array(
        'price'          => '67.79',
        'tax_percentage' => 19,
        'quantity'       => 10,
        'discount'       => 10,
        'discount_type'  => 'percentage'));

$purchasePrice = new \PTX\PurchasePrice();
$total_price = $this->PurchasePrice->count_total($products_data);

/**
 * RESULT
 * array(
 *    'total' => 909.9,
 *     'subtotal' => 764.62,
 *     'tax' => array(
 *         19 => 145.28),
 *     'discount' => 167.8);
 */
```