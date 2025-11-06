import React, { useEffect, useState, useCallback } from 'react';
import { Page, Card, TextField, Select, Pagination, Spinner } from '@shopify/polaris';
import axios from 'axios';
import ProductTable from './ProductTable';

const Products = () => {
  const [products, setProducts] = useState([]);
  const [status, setStatus] = useState('all');
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchProducts = useCallback(() => {
    setLoading(true);
    axios.get('/api/products', {
      params: {
        page,
        search,
        status,
      },
    })
      .then((res) => {
        setProducts(res.data.data || []);
        setTotalPages(res.data.last_page || 1);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  }, [page, search, status]);

  useEffect(() => {
    fetchProducts();
  }, [fetchProducts]);

  return (
    <Page title="Products">
      <Card sectioned>
        <div style={{ display: 'flex', gap: 8 }}>
          <TextField
            label="Search by title"
            value={search}
            onChange={setSearch}
            placeholder="Search products..."
            autoComplete="off"
          />
          <Select
            label="Filter by status"
            options={[
              { label: 'All', value: 'all' },
              { label: 'Active', value: 'active' },
              { label: 'Draft', value: 'draft' },
              { label: 'Archived', value: 'archived' },
            ]}
            onChange={setStatus}
            value={status}
          />
        </div>
      </Card>

      <Card>
        {loading ? (
          <div style={{ padding: 30, textAlign: 'center' }}>
            <Spinner accessibilityLabel="Loading products" size="large" />
          </div>
        ) : (
          <ProductTable products={products} />
        )}
      </Card>

      <div style={{ marginTop: 20, display: 'flex', justifyContent: 'center' }}>
        <Pagination
          hasPrevious={page > 1}
          onPrevious={() => setPage((p) => p - 1)}
          hasNext={page < totalPages}
          onNext={() => setPage((p) => p + 1)}
        />
      </div>
    </Page>
  );
};

export default Products;
