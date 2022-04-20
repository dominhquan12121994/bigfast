<?php
/**
 * Copyright (c) 2020. Electric
 */
use Picqer\Barcode\BarcodeGeneratorPNG;

$barcode = new BarcodeGeneratorPNG();

return [
    'short' => $barcode::TYPE_CODE_128,
    'long' => $barcode::TYPE_CODE_128,
];