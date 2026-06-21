import http from '@/api/http';

export interface Announcement {
    tag: string;
    cls: string;
    offer: boolean;
    title: string;
    body: string;
    link: string | null;
}

export default (): Promise<Announcement[]> => http.get('/api/client/announcements').then(({ data }) => data.data || []);
