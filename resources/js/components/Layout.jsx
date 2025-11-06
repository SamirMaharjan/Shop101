import React from 'react';
import { Frame, Navigation } from '@shopify/polaris';
import { Outlet, useNavigate, useLocation } from 'react-router-dom';
import { HomeIcon as HomeMajor, ProductIcon  as ProductsMajor,RefreshIcon } from '@shopify/polaris-icons';

const Layout = () => {
  const navigate = useNavigate();
  const location = useLocation();

  const items = [
    {
      label: 'Dashboard',
      icon: HomeMajor,
      onClick: () => navigate('/'),
      selected: location.pathname === '/',
    },
    {
      label: 'Products',
      icon: ProductsMajor,
      onClick: () => navigate('/products'),
      selected: location.pathname === '/products',
    },
    {
      label: 'Sync Logs',
      icon: RefreshIcon,
      onClick: () => navigate('/sync-logs'),
      selected: location.pathname === '/sync-logs',
    },
  ];

  return (
    <Frame
      navigation={
        <Navigation location={location.pathname}>
          <Navigation.Section items={items} />
        </Navigation>
      }
    >
      <div style={{ padding: 24 }}>
        <Outlet />
      </div>
    </Frame>
  );
};

export default Layout;
