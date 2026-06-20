import React from 'react';
import { Link, NavLink, useRouteMatch } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { ServerContext } from '@/state/server';
import Can from '@/components/elements/Can';
import routes from '@/routers/routes';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faArchive,
    faArrowLeft,
    faCalendarAlt,
    faCog,
    faDatabase,
    faExternalLinkAlt,
    faFolder,
    faGamepad,
    faNetworkWired,
    faServer,
    faSlidersH,
    faTerminal,
    faUsers,
    faWaveSquare,
} from '@fortawesome/free-solid-svg-icons';

// Map server sub-page names to FontAwesome v5 (free-solid) icons. Keep these to
// icons available in the installed FA5 set.
const ICONS: Record<string, typeof faServer> = {
    Console: faTerminal,
    Players: faGamepad,
    Files: faFolder,
    Databases: faDatabase,
    Schedules: faCalendarAlt,
    Users: faUsers,
    Backups: faArchive,
    Network: faNetworkWired,
    Startup: faSlidersH,
    Settings: faCog,
    Activity: faWaveSquare,
};

// Sidebar navigation for the in-server pages. Mirrors the per-server routes
// (gated by permission) that used to live in the old SubNavigation, rendered in
// the new shell's sidebar.
export default () => {
    const match = useRouteMatch();
    const name = ServerContext.useStoreState((state) => state.server.data?.name);
    const serverId = ServerContext.useStoreState((state) => state.server.data?.internalId);
    const rootAdmin = useStoreState((state) => state.user.data!.rootAdmin);

    const to = (value: string): string => {
        if (value === '/') {
            return match.url;
        }
        return `${match.url.replace(/\/*$/, '')}/${value.replace(/^\/+/, '')}`;
    };

    return (
        <nav className={'nav'}>
            <Link to={'/'} className={'back'}>
                <FontAwesomeIcon icon={faArrowLeft} /> All servers
            </Link>
            <div className={'svh'}>
                <FontAwesomeIcon icon={faServer} />
                <span>{name || 'Server'}</span>
            </div>

            <div className={'sec'}>Server</div>
            {routes.server
                .filter((route) => !!route.name)
                .map((route) => {
                    const link = (
                        <NavLink to={to(route.path)} exact={route.exact} activeClassName={'on'}>
                            <FontAwesomeIcon icon={ICONS[route.name as string] || faServer} /> {route.name}
                        </NavLink>
                    );
                    return route.permission ? (
                        <Can key={route.path} action={route.permission} matchAny>
                            {link}
                        </Can>
                    ) : (
                        <React.Fragment key={route.path}>{link}</React.Fragment>
                    );
                })}

            {rootAdmin && serverId && (
                <>
                    <div className={'sec'}>Admin</div>
                    {/* eslint-disable-next-line react/jsx-no-target-blank */}
                    <a href={`/admin/servers/view/${serverId}`} target={'_blank'} rel={'noreferrer'}>
                        <FontAwesomeIcon icon={faExternalLinkAlt} /> Admin View
                    </a>
                </>
            )}
        </nav>
    );
};
