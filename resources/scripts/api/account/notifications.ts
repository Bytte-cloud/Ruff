import http from '@/api/http';

export type NotificationCategory = 'event' | 'billing' | 'maintenance' | 'system';

export interface AppNotification {
    id: string;
    category: NotificationCategory;
    title: string;
    message: string;
    actionUrl: string | null;
    read: boolean;
    createdAt: Date;
}

export interface NotificationsResult {
    items: AppNotification[];
    unreadCount: number;
}

export const getNotifications = async (): Promise<NotificationsResult> => {
    const { data } = await http.get('/api/client/account/notifications');

    return {
        items: (data.data || []).map((datum: any) => ({
            id: datum.attributes.id,
            category: datum.attributes.category,
            title: datum.attributes.title,
            message: datum.attributes.message,
            actionUrl: datum.attributes.action_url ?? null,
            read: !!datum.attributes.read,
            createdAt: new Date(datum.attributes.created_at),
        })),
        unreadCount: data.meta?.unread_count ?? 0,
    };
};

export const markNotificationRead = async (id: string): Promise<void> => {
    await http.post(`/api/client/account/notifications/${id}/read`);
};

export const markAllNotificationsRead = async (): Promise<void> => {
    await http.post('/api/client/account/notifications/read');
};

export const deleteNotification = async (id: string): Promise<void> => {
    await http.delete(`/api/client/account/notifications/${id}`);
};
