import React from 'react';
import { IndexTable, Text, Thumbnail, Card } from '@shopify/polaris';

const ProductTable = ({ products }) => {
  return (
    <Card>
      <IndexTable
        resourceName={{ singular: 'product', plural: 'products' }}
        itemCount={products.length}
        headings={[
          { title: 'Image' },
          { title: 'Title' },
          { title: 'Status' },
          { title: 'Price' },
        ]}
      >
        {products.map((p, index) => (
          <IndexTable.Row id={p.id} key={p.id} position={index}>
            <IndexTable.Cell>
              <Thumbnail
                source={
                  Array.isArray(p.images) && p.images.length > 0
                    ? p.images[0] // take the first image
                    : 'https://cdn.shopify.com/s/files/1/0533/2089/files/placeholder-images-image_large.png'
                }
                alt={p.title}
              />
            </IndexTable.Cell>
            <IndexTable.Cell><Text variant="bodyMd">{p.title}</Text></IndexTable.Cell>
            <IndexTable.Cell><Text variant="bodyMd">{p.status.charAt(0).toUpperCase() + p.status.slice(1).toLowerCase()}</Text></IndexTable.Cell>
            <IndexTable.Cell><Text variant="bodyMd">Rs {p.price}</Text></IndexTable.Cell>
          </IndexTable.Row>
        ))}
      </IndexTable>
    </Card>
  );
};

export default ProductTable;
