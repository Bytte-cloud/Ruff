import React, { useEffect, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faArrowRight,
    faChevronDown,
    faChevronRight,
    faFolderPlus,
    faList,
    faPen,
    faPlus,
    faThLarge,
    faTrash,
} from '@fortawesome/free-solid-svg-icons';
import { Server } from '@/api/server/getServer';
import getServers from '@/api/getServers';
import ServerRow from '@/components/dashboard/ServerRow';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import { usePersistedState } from '@/plugins/usePersistedState';
import useSWR from 'swr';
import { PaginatedResult } from '@/api/http';
import Pagination from '@/components/elements/Pagination';
import { useLocation } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';
import {
    assignServerToFolder,
    createFolder,
    deleteFolder,
    getFolders,
    ServerFolder,
    updateFolder,
} from '@/api/account/folders';

// Placeholder announcement/offer/blog content. These will be admin-managed later.
const ANNOUNCEMENTS = [
    {
        tag: 'Offer',
        cls: 'o',
        title: '50% off your second server',
        body: 'Deploy another game server this week and get half off the first month.',
        offer: true,
    },
    {
        tag: 'Announcement',
        cls: 'a',
        title: 'Scheduled maintenance',
        body: 'EU-West nodes reboot Sunday 02:00–03:00 UTC. Expect brief downtime.',
        offer: false,
    },
    {
        tag: 'Blog',
        cls: 'b',
        title: 'One-click modpack installs',
        body: 'Deploy CurseForge & Modrinth packs straight from the egg list.',
        offer: false,
    },
];

export default () => {
    const { search } = useLocation();
    const defaultPage = Number(new URLSearchParams(search).get('page') || '1');

    const [page, setPage] = useState(!isNaN(defaultPage) && defaultPage > 0 ? defaultPage : 1);
    const [view, setView] = useState<'list' | 'grid'>('list');
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const uuid = useStoreState((state) => state.user.data!.uuid);
    const rootAdmin = useStoreState((state) => state.user.data!.rootAdmin);
    const [showOnlyAdmin, setShowOnlyAdmin] = usePersistedState(`${uuid}:show_all_servers`, false);

    const { data: servers, error } = useSWR<PaginatedResult<Server>>(
        ['/api/client/servers', showOnlyAdmin && rootAdmin, page],
        () => getServers({ page, type: showOnlyAdmin && rootAdmin ? 'admin' : undefined })
    );

    const { data: folders, mutate: mutateFolders } = useSWR<ServerFolder[]>(
        ['/api/client/account/folders', uuid],
        () => getFolders(),
        { revalidateOnFocus: false }
    );

    const [creating, setCreating] = useState(false);
    const [newName, setNewName] = useState('');
    const [collapsed, setCollapsed] = usePersistedState<Record<string, boolean>>(`${uuid}:folder_collapsed`, {});

    useEffect(() => {
        if (!servers) return;
        if (servers.pagination.currentPage > 1 && !servers.items.length) {
            setPage(1);
        }
    }, [servers?.pagination.currentPage]);

    useEffect(() => {
        window.history.replaceState(null, document.title, `/${page <= 1 ? '' : `?page=${page}`}`);
    }, [page]);

    useEffect(() => {
        if (error) clearAndAddHttpError({ key: 'dashboard', error });
        if (!error) clearFlashes('dashboard');
    }, [error]);

    const folderList = folders || [];
    const total = servers?.pagination.total ?? 0;

    const folderOf = (serverUuid: string): string | null =>
        folderList.find((f) => f.servers.includes(serverUuid))?.uuid ?? null;

    const handleCreate = () => {
        const name = newName.trim();
        if (!name) return;
        createFolder(name)
            .then(() => mutateFolders())
            .then(() => {
                setNewName('');
                setCreating(false);
            })
            .catch((err) => clearAndAddHttpError({ key: 'dashboard', error: err }));
    };

    const handleRename = (folder: ServerFolder) => {
        const name = window.prompt('Rename folder', folder.name);
        if (name === null) return;
        const trimmed = name.trim();
        if (!trimmed || trimmed === folder.name) return;
        updateFolder(folder.uuid, { name: trimmed })
            .then(() => mutateFolders())
            .catch((err) => clearAndAddHttpError({ key: 'dashboard', error: err }));
    };

    const handleDelete = (folder: ServerFolder) => {
        if (!window.confirm(`Delete the folder "${folder.name}"? The servers inside it are not removed.`)) return;
        deleteFolder(folder.uuid)
            .then(() => mutateFolders())
            .catch((err) => clearAndAddHttpError({ key: 'dashboard', error: err }));
    };

    const handleMove = (serverUuid: string, folderUuid: string | null) => {
        assignServerToFolder(serverUuid, folderUuid)
            .then(() => mutateFolders())
            .catch((err) => clearAndAddHttpError({ key: 'dashboard', error: err }));
    };

    const toggleCollapsed = (key: string) => setCollapsed((c) => ({ ...(c || {}), [key]: !(c || {})[key] }));

    const renderList = (list: Server[], folderUuid: string | null) =>
        view === 'grid' ? (
            <div className={'grid'}>
                {list.map((server) => (
                    <ServerRow
                        key={server.uuid}
                        server={server}
                        grid
                        folders={folderList}
                        currentFolder={folderUuid}
                        onMove={(f) => handleMove(server.uuid, f)}
                    />
                ))}
            </div>
        ) : (
            <div className={'tbl'}>
                {list.map((server) => (
                    <ServerRow
                        key={server.uuid}
                        server={server}
                        folders={folderList}
                        currentFolder={folderUuid}
                        onMove={(f) => handleMove(server.uuid, f)}
                    />
                ))}
            </div>
        );

    return (
        <>
            <FlashMessageRender byKey={'dashboard'} />

            <div className={'news'}>
                {ANNOUNCEMENTS.map((a) => (
                    <div key={a.title} className={`ann ${a.offer ? 'offer' : ''}`}>
                        <span className={`tag ${a.cls}`}>{a.tag}</span>
                        <h4>{a.title}</h4>
                        <p>{a.body}</p>
                        <span className={'more'}>
                            {a.offer ? 'Claim offer' : 'Read more'} <FontAwesomeIcon icon={faArrowRight} />
                        </span>
                    </div>
                ))}
            </div>

            <div className={'ph'}>
                <h1>Servers</h1>
                <span className={'c'}>
                    {total} server{total === 1 ? '' : 's'}
                    {rootAdmin && (
                        <button
                            onClick={() => {
                                setShowOnlyAdmin((s) => !s);
                                setPage(1);
                            }}
                            style={{
                                marginLeft: 10,
                                background: 'none',
                                border: 0,
                                color: 'hsl(var(--c-accent-400))',
                                fontFamily: 'inherit',
                                fontWeight: 600,
                                cursor: 'pointer',
                            }}
                        >
                            {showOnlyAdmin ? '· viewing all' : '· view all'}
                        </button>
                    )}
                </span>
                <div className={'r'}>
                    <div className={'seg'}>
                        <button className={view === 'list' ? 'on' : ''} onClick={() => setView('list')}>
                            <FontAwesomeIcon icon={faList} />
                        </button>
                        <button className={view === 'grid' ? 'on' : ''} onClick={() => setView('grid')}>
                            <FontAwesomeIcon icon={faThLarge} />
                        </button>
                    </div>
                    {creating ? (
                        <div className={'fnew'}>
                            <input
                                autoFocus
                                value={newName}
                                placeholder={'Folder name'}
                                onChange={(e) => setNewName(e.currentTarget.value)}
                                onKeyDown={(e) => {
                                    if (e.key === 'Enter') handleCreate();
                                    if (e.key === 'Escape') {
                                        setCreating(false);
                                        setNewName('');
                                    }
                                }}
                            />
                            <button className={'nb'} onClick={handleCreate}>
                                Add
                            </button>
                            <button
                                className={'fghost'}
                                onClick={() => {
                                    setCreating(false);
                                    setNewName('');
                                }}
                            >
                                Cancel
                            </button>
                        </div>
                    ) : (
                        <button className={'fghost'} onClick={() => setCreating(true)}>
                            <FontAwesomeIcon icon={faFolderPlus} /> New Folder
                        </button>
                    )}
                    <button className={'nb'}>
                        <FontAwesomeIcon icon={faPlus} /> New Server
                    </button>
                </div>
            </div>

            {!servers ? (
                <div className={'spin-wrap'}>
                    <Spinner size={'large'} />
                </div>
            ) : (
                <Pagination data={servers} onPageSelect={setPage}>
                    {({ items }) => {
                        if (items.length === 0) {
                            return (
                                <p className={'empty'}>
                                    {showOnlyAdmin
                                        ? 'There are no other servers to display.'
                                        : 'There are no servers associated with your account.'}
                                </p>
                            );
                        }

                        // No folders configured — keep the simple flat list with a header row.
                        if (folderList.length === 0) {
                            return view === 'grid' ? (
                                renderList(items, null)
                            ) : (
                                <div className={'tbl'}>
                                    <div className={'th'}>
                                        <div>Server</div>
                                        <div>Status</div>
                                        <div>Connect</div>
                                        <div>Load</div>
                                        <div>Power</div>
                                        <div />
                                    </div>
                                    {items.map((server) => (
                                        <ServerRow
                                            key={server.uuid}
                                            server={server}
                                            folders={folderList}
                                            currentFolder={null}
                                            onMove={(f) => handleMove(server.uuid, f)}
                                        />
                                    ))}
                                </div>
                            );
                        }

                        const ungrouped = items.filter((s) => folderOf(s.uuid) === null);

                        return (
                            <>
                                {folderList.map((folder) => {
                                    const inFolder = items.filter((s) => folderOf(s.uuid) === folder.uuid);
                                    const isCollapsed = !!(collapsed || {})[folder.uuid];
                                    return (
                                        <section key={folder.uuid} className={'fsec'}>
                                            <header className={'fsec-h'}>
                                                <button
                                                    className={'fsec-t'}
                                                    onClick={() => toggleCollapsed(folder.uuid)}
                                                >
                                                    <FontAwesomeIcon
                                                        icon={isCollapsed ? faChevronRight : faChevronDown}
                                                        className={'fcar'}
                                                    />
                                                    <i
                                                        className={'fdot'}
                                                        style={{ background: folder.color || undefined }}
                                                    />
                                                    {folder.name}
                                                    <span className={'fcount'}>{folder.servers.length}</span>
                                                </button>
                                                <div className={'fsec-a'}>
                                                    <button onClick={() => handleRename(folder)} aria-label={'Rename'}>
                                                        <FontAwesomeIcon icon={faPen} />
                                                    </button>
                                                    <button onClick={() => handleDelete(folder)} aria-label={'Delete'}>
                                                        <FontAwesomeIcon icon={faTrash} />
                                                    </button>
                                                </div>
                                            </header>
                                            {!isCollapsed &&
                                                (inFolder.length > 0 ? (
                                                    renderList(inFolder, folder.uuid)
                                                ) : (
                                                    <p className={'fempty'}>No servers in this folder on this page.</p>
                                                ))}
                                        </section>
                                    );
                                })}

                                {ungrouped.length > 0 && (
                                    <section className={'fsec'}>
                                        <header className={'fsec-h'}>
                                            <span className={'fsec-t'}>
                                                Ungrouped
                                                <span className={'fcount'}>{ungrouped.length}</span>
                                            </span>
                                        </header>
                                        {renderList(ungrouped, null)}
                                    </section>
                                )}
                            </>
                        );
                    }}
                </Pagination>
            )}
        </>
    );
};
