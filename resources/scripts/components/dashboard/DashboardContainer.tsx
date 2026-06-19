import React, { useEffect, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faArrowRight, faList, faPlus, faThLarge } from '@fortawesome/free-solid-svg-icons';
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

    const total = servers?.pagination.total ?? 0;

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
                    {({ items }) =>
                        items.length > 0 ? (
                            view === 'grid' ? (
                                <div className={'grid'}>
                                    {items.map((server) => (
                                        <ServerRow key={server.uuid} server={server} grid />
                                    ))}
                                </div>
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
                                        <ServerRow key={server.uuid} server={server} />
                                    ))}
                                </div>
                            )
                        ) : (
                            <p className={'empty'}>
                                {showOnlyAdmin
                                    ? 'There are no other servers to display.'
                                    : 'There are no servers associated with your account.'}
                            </p>
                        )
                    }
                </Pagination>
            )}
        </>
    );
};
