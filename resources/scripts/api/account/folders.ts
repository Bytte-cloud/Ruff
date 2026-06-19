import http, { FractalResponseData, FractalResponseList } from '@/api/http';

export interface ServerFolder {
    uuid: string;
    name: string;
    color: string | null;
    sort: number;
    /** UUIDs of the servers filed under this folder. */
    servers: string[];
    createdAt: Date;
    updatedAt: Date;
}

export const rawDataToServerFolder = (data: any): ServerFolder => ({
    uuid: data.uuid,
    name: data.name,
    color: data.color ?? null,
    sort: data.sort ?? 0,
    servers: data.servers || [],
    createdAt: new Date(data.created_at),
    updatedAt: new Date(data.updated_at),
});

export const getFolders = async (): Promise<ServerFolder[]> => {
    const { data } = await http.get('/api/client/account/folders');

    return (data as FractalResponseList).data.map((datum: FractalResponseData) =>
        rawDataToServerFolder(datum.attributes)
    );
};

export const createFolder = async (name: string, color?: string | null): Promise<ServerFolder> => {
    const { data } = await http.post('/api/client/account/folders', { name, color: color ?? null });

    return rawDataToServerFolder(data.attributes);
};

export const updateFolder = async (
    uuid: string,
    values: { name?: string; color?: string | null; sort?: number }
): Promise<ServerFolder> => {
    const { data } = await http.patch(`/api/client/account/folders/${uuid}`, values);

    return rawDataToServerFolder(data.attributes);
};

export const deleteFolder = async (uuid: string): Promise<void> => {
    await http.delete(`/api/client/account/folders/${uuid}`);
};

/** Assign a server to a folder, or pass folder=null to remove it from all folders. */
export const assignServerToFolder = async (server: string, folder: string | null): Promise<void> => {
    await http.post('/api/client/account/folders/assign', { server, folder });
};
