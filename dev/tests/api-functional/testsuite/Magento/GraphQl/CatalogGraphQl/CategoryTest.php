<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\CatalogGraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test class to verify category uid, available as product aggregation type
 */
class CategoryTest extends GraphQlAbstract
{
    /**
     * Test for checking if graphQL query for category uid is available as product aggregation type
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testCheckCategoryUidAsAggregation(): void
    {
        $query = $this->getSearchQueryWithSCategoryUID();
        $response = $this->graphQlQuery($query);
        $this->assertNotEmpty($response['product']);
        $this->assertEquals(1, count($response['product']['aggregations']));
        $this->assertNotEmpty($response['product']['aggregations']);
        $this->assertEquals('price', $response['product']['aggregations'][0]['attribute_code']);
    }

    /**
     * Prepare search query with suggestions
     *
     * @return string
     */
    private function getSearchQueryWithSCategoryUID() : string
    {
        return <<<QUERY
{
  product(filter: {category_uid: {eq: "Mg=="}}) {
    aggregations {
        __typename
        attribute_code
    }
    __typename
  }
}
QUERY;
    }
}
