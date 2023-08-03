<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class VirtualProductViewTest extends GraphQlAbstract
{
    /**
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testQueryAllFieldsVirtualProduct()
    {
        $productSku = 'virtual-product';

        $query
            = <<<QUERY
{
   product(filter: {sku: {eq: "$productSku"}})
   {
       items{
           id
           name
           sku
           type_id
           ... on PhysicalProductInterface {
             weight
           }
           ... on VirtualProduct {
            name
            id
            sku
           }
       }
   }
}
QUERY;

        $response = $this->graphQlQuery($query);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
        $product = $productRepository->get($productSku, false, null, true);
        $this->assertArrayHasKey('product', $response);
        $this->assertArrayHasKey('items', $response['product']);
        $this->assertCount(1, $response['product']['items']);
        $this->assertArrayHasKey(0, $response['product']['items']);
        $this->assertBaseFields($product, $response['product']['items'][0]);
        $this->assertArrayNotHasKey(
            'weight',
            $response['product']['items'][0],
            "response does contain the key weight"
        );
    }

    /**
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testCannotQueryWeightOnVirtualProductException()
    {
        $productSku = 'virtual-product';

        $query
            = <<<QUERY
{
   product(filter: {sku: {eq: "$productSku"}})
   {
       items{
           id
           name
           sku
           type_id
           ... on PhysicalProductInterface {
             weight
           }
           ... on VirtualProduct {
            name
            weight
            id
            sku
           }
       }
   }
}
QUERY;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'GraphQL response contains errors: Cannot query field "weight" on type "VirtualProduct"'
        );
        $this->graphQlQuery($query);
    }

    /**
     * @param ProductInterface $product
     * @param array $actualResponse
     */
    private function assertBaseFields($product, $actualResponse)
    {
        $assertionMap = [
            ['response_field' => 'id', 'expected_value' => $product->getId()],
            ['response_field' => 'name', 'expected_value' => $product->getName()],
            ['response_field' => 'sku', 'expected_value' => $product->getSku()],
            ['response_field' => 'type_id', 'expected_value' => $product->getTypeId()]
        ];

        $this->assertResponseFields($actualResponse, $assertionMap);
    }
}
