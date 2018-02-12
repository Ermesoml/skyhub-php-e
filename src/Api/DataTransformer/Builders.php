<?php

namespace SkyHub\Api\DataTransformer;

use SkyHub\Api\DataTransformer\Catalog\Product\Variation\Create;
use SkyHub\Api\Helpers;

/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  SkuHub
 * @package   SkuHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */
trait Builders
{
    
    use Helpers;


    /**
     * @param array $productData
     * @param array $images
     *
     * @return array
     */
    protected function buildProductImages(array &$productData, array $images)
    {
        /** @var string $image */
        foreach ($images as $image) {
            $productData['images'][] = (string) $image;
        }

        return $productData;
    }


    /**
     * @param array $productData
     * @param array $specifications
     *
     * @return array
     */
    protected function buildProductSpecifications(array &$productData, array $specifications)
    {
        $specifications[] = [
            'key' => 'category_ids',
            'value' => [
                1, 2
            ],
        ];

        /** @var array $specification */
        foreach ($specifications as $specification) {
            $key   = $this->arrayExtract($specification, 'key', '');
            $value = $this->arrayExtract($specification, 'value', '');

            if (is_array($key) || is_array($value)) {
                continue;
            }

            $productData['specifications'][] = [
                'key'   => (string) $key,
                'value' => (string) $value,
            ];
        }

        return $productData;
    }


    /**
     * @param array $productData
     * @param array $categories
     *
     * @return array
     */
    protected function buildProductCategories(array &$productData, array $categories)
    {
        /** @var array $categories */
        foreach ($categories as $category) {
            $productData['categories'][] = [
                'code' => (string) $this->arrayExtract($category, 'code', ''),
                'name' => (string) $this->arrayExtract($category, 'name', ''),
            ];
        }

        return $productData;
    }


    /**
     * @param array $productData
     * @param array $attributes
     *
     * @return array
     */
    protected function buildProductVariationAttributes(array &$productData, array $attributes)
    {
        /** @var string $attribute */
        foreach ($attributes as $attribute) {
            $productData['variation_attributes'][] = (string) $attribute;
        }

        return $productData;
    }


    /**
     * @param array  $productData
     * @param string $sku
     * @param string $qty
     * @param string $ean
     * @param array  $images
     * @param array  $specifications
     *
     * @return array
     */
    protected function buildProductVariation(
        array &$productData,
        $sku,
        $qty,
        $ean,
        array $images = [],
        array $specifications = []
    ) {
        $transformer = new Create($sku, $qty, $ean, $images, $specifications);
        $output      = $transformer->output();

        $productData['variations'][] = $output['variation'];

        return $productData;
    }
}
