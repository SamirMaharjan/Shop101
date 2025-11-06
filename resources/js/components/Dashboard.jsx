import React, { useEffect, useState } from 'react';
import { Page, Layout, Card, TextContainer, Text } from '@shopify/polaris';
import axios from 'axios';

const Dashboard = () => {
  const [summary, setSummary] = useState({
    products: 0,
    collections: 0,
    lastSync: null,
  });

  useEffect(() => {
    axios.get('/api/dashboard-summary')
      .then((res) => setSummary(res.data))
      .catch(console.error);
  }, []);

  return (
    <Page title="Dashboard Overview">
      <Layout>
        <Layout.Section oneThird>
          <Card>
            <div>
              <Text as="h2" variant="headingMd">Products</Text>
              <Text>{summary.products}</Text>
            </div>
          </Card>
        </Layout.Section>

        <Layout.Section oneThird>
          <Card>
            <div>
              <Text as="h2" variant="headingMd">Collections</Text>
              <Text>{summary.collections}</Text>
            </div>
          </Card>
        </Layout.Section>

        <Layout.Section oneThird>
          <Card>
            <div>
              <Text as="h2" variant="headingMd">Last Sync</Text>
              <Text>{summary.lastSync ? new Date(summary.lastSync).toLocaleString() : 'Never synced'}</Text>
            </div>
          </Card>
        </Layout.Section>
      </Layout>
    </Page>
  );
};

export default Dashboard;
