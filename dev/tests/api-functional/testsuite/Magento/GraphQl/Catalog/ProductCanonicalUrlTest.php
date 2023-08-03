<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Catalog;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for getting canonical_url for product
 */
class ProductCanonicalUrlTest extends GraphQlAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture default_store catalog/seo/product_canonical_tag 1
     *
     */
    public function testProductWithCanonicalLinksMetaTagSettingsEnabled()
    {
        $productSku = 'simple';
        $query
            = <<<QUERY
{
    product (filter: {sku: {eq: "{$productSku}"}}) {
        items {
            name
            sku
            canonical_url
        }
    }
}
QUERY;

        $response = $this->graphQlQuery($query);
        $this->assertNotEmpty($response['product']['items']);

        $this->assertEquals(
            'simple-product.html',
            $response['product']['items'][0]['canonical_url']
        );
        $this->assertEquals('simple', $response['product']['items'][0]['sku']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture default_store catalog/seo/product_canonical_tag 0
     */
    public function testProductWithCanonicalLinksMetaTagSettingsDisabled()
    {
        $productSku = 'simple';
        $query
            = <<<QUERY
{
    product (filter: {sku: {eq: "{$productSku}"}}) {
        items {
            name
            sku
            canonical_url
        }
    }
}
QUERY;

        $response = $this->graphQlQuery($query);
        $this->assertNull(
            $response['product']['items'][0]['canonical_url']
        );
        $this->assertEquals('simple', $response['product']['items'][0]['sku']);
    }
}
