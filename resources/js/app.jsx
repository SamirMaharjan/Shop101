import React from 'react';
import { createRoot } from 'react-dom/client';
import {
  createBrowserRouter,
  RouterProvider,
} from 'react-router-dom';

import Layout from './components/Layout';
import Dashboard from './components/Dashboard';
import ProductList from './components/ProductList';
import { AppProvider } from '@shopify/polaris';
import en from '@shopify/polaris/locales/en.json';
import '@shopify/polaris/build/esm/styles.css'; // ✅ Polaris styles
import SyncLogs from './components/SyncLogs';


const router = createBrowserRouter([
  {
    path: '/',
    element: <Layout />,
    children: [
      { path: '/', element: <Dashboard /> },
      { path: '/products', element: <ProductList /> },
      { path: '/sync-logs', element: <SyncLogs /> },
    ],
  },
]);

// const root = createRoot(document.getElementById('app'));
// root.render(<RouterProvider router={router} />);

createRoot(document.getElementById('app')).render(
  <AppProvider i18n={en}>
    <RouterProvider router={router} />
  </AppProvider>
);
