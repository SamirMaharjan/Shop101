import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ReCAPTCHA from 'react-google-recaptcha';
import {
  Card,
  Button,
  Spinner,
  DataTable,
  Page,
  Banner,
  Toast,
  Frame,
} from '@shopify/polaris';

const SyncLogs = () => {
  const [logs, setLogs] = useState([]);
  const [loading, setLoading] = useState(false);
  const [syncing, setSyncing] = useState(false);
  const [captchaVisible, setCaptchaVisible] = useState(false);
  const [toast, setToast] = useState({ active: false, message: '', error: false });

const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY; 

  // Fetch logs on mount
  useEffect(() => {
    fetchLogs();
  }, []);

  const fetchLogs = async () => {
    try {
      setLoading(true);
      const response = await axios.get('/api/sync-logs');
      setLogs(response.data.data);
      
    } catch (error) {
      console.error(error);
      showToast('Failed to fetch logs', true);
    } finally {
      setLoading(false);
    }
  };

  const showToast = (message, error = false) => {
    setToast({ active: true, message, error });
  };

  const handleSyncClick = () => {
    setCaptchaVisible(true);
  };

  const handleRecaptchaVerify = async (token) => {
    if (!token) return;

    try {
      setCaptchaVisible(false);
      setSyncing(true);
      const response = await axios.post('/api/products/sync', { recaptcha_token: token });
      showToast(response.data.message || 'Sync started successfully');
      fetchLogs(); // refresh log list after sync
    } catch (error) {
      console.error(error);
      showToast('Failed to sync products', true);
    } finally {
      setSyncing(false);
    }
  };

  return (
    <Frame>
      <Page title="Product Sync Logs">
        <Card sectioned>
          <Button
            primary
            onClick={handleSyncClick}
            loading={syncing}
            disabled={syncing}
          >
            Sync Products
          </Button>

          {captchaVisible && (
            <div style={{ marginTop: 16 }}>
              <ReCAPTCHA
                sitekey={RECAPTCHA_SITE_KEY}
                onChange={handleRecaptchaVerify}
              />
            </div>
          )}
        </Card>

        <Card title="Sync Logs" sectioned>
          {loading ? (
            <div style={{ textAlign: 'center', padding: 20 }}>
              <Spinner accessibilityLabel="Loading logs" size="large" />
            </div>
          ) : logs.length === 0 ? (
            <Banner status="info">No logs found.</Banner>
          ) : (
            <DataTable
              columnContentTypes={['text', 'text', 'text']}
              headings={['Date', 'Sync Type', 'Status']}
              rows={logs.map((log) => [
                new Date(log.created_at).toLocaleString(),
                log.sync_type,
                log.status,
              ])}
            />
          )}
        </Card>

        {toast.active && (
          <Toast
            content={toast.message}
            error={toast.error}
            onDismiss={() => setToast({ ...toast, active: false })}
          />
        )}
      </Page>
    </Frame>
  );
};

export default SyncLogs;
