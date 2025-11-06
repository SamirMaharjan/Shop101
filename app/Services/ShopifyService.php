<?php

namespace App\Services;

use App\Http\Resources\ProductOriginResource;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    public function graphqlRequest($shop, $accessToken, $query)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shop}/admin/api/2025-10/graphql.json", [
            'query' => $query,
        ]);

        return $response->json();
    }

    public function fetchProducts($shop, $accessToken)
    {
        $query = <<<'GRAPHQL'
        {
            products(first: 50) {
                edges {
                    node {
                        id
                        title
                        status
                        tags
                        collections(first: 50) { edges { node { id title } } }
                        images(first: 1) { edges { node { src } } }
                        variants(first: 1) { edges { node { price } } }
                    }
                }
            }
        }
        GRAPHQL;

        return $this->graphqlRequest($shop, $accessToken, $query);
    }
    public function fetchAllProducts($shop, $accessToken)
    {
        $allProducts = [];
        $cursor = null;

        do {
            $query = '
                        {
                            products(first: 50' . ($cursor ? ', after: "' . $cursor . '"' : '') . ') {
                                edges {
                                    cursor
                                    node {
                                        id
                                        title
                                        status
                                        tags
                                        collections(first: 50) { edges { node { id title } } }
                                        images(first: 1) { edges { node { src } } }
                                        variants(first: 1) { edges { node { price } } }
                                    }
                                }
                                pageInfo {
                                    hasNextPage
                                    endCursor
                                }
                            }
                        }';

            $response = $this->graphqlRequest($shop, $accessToken, $query);

            foreach ($response['data']['products']['edges'] as $edge) {
                $allProducts[] = $edge['node'];
            }

            $cursor = $response['data']['products']['pageInfo']['endCursor'];
            $hasNext = $response['data']['products']['pageInfo']['hasNextPage'];
        } while ($hasNext);

        // $allProducts now contains all products
        // return $allProducts;

       $transformed = array_map(fn($allProducts) => (new ProductOriginResource(collect($allProducts)))->toArray(request()), $allProducts);

    return $transformed;

    }
    public function fetchAllCollections($shop, $accessToken)
    {
        $allCollections = [];
        $cursor = null;

        do {
            $query = '
                        {
                            collections(first: 50' . ($cursor ? ', after: "' . $cursor . '"' : '') . ') {
                                edges {
                                    cursor
                                    node {
                                        id
                                        title
                                    }
                                }
                                pageInfo {
                                    hasNextPage
                                    endCursor
                                }
                            }
                        }';

            $response = $this->graphqlRequest($shop, $accessToken, $query);
            // dd($response);
            foreach ($response['data']['collections']['edges'] as $edge) {
                $allCollections[] = $edge['node'];
            }

            $cursor = $response['data']['collections']['pageInfo']['endCursor'];
            $hasNext = $response['data']['collections']['pageInfo']['hasNextPage'];
        } while ($hasNext);

        // $allProducts now contains all products
        // dd($allCollections);
        return $allCollections;

    }
}
