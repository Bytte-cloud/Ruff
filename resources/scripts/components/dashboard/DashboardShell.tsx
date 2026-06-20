import React from 'react';
import Shell from '@/components/dashboard/Shell';
import DashboardSidebar from '@/components/dashboard/DashboardSidebar';

// The dashboard/account pages render inside the shared client shell with the
// dashboard sidebar.
export default ({ children }: { children: React.ReactNode }) => (
    <Shell sidebar={<DashboardSidebar />}>{children}</Shell>
);
