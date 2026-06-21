import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faMicrochip, faSearch, faServer } from '@fortawesome/free-solid-svg-icons';
import NotificationCenter from '@/components/dashboard/NotificationCenter';
import UserMenu from '@/components/dashboard/UserMenu';
import SearchModal from '@/components/dashboard/search/SearchModal';
import useEventListener from '@/plugins/useEventListener';

interface Props {
    // The sidebar navigation for this context (dashboard vs. a single server).
    sidebar: React.ReactNode;
    // "server" relaxes the main content padding so in-server pages (which bring
    // their own ContentContainer spacing) don't end up double-padded.
    variant?: 'dashboard' | 'server';
    children: React.ReactNode;
}

// Shell is the shared client chrome: the top bar (wordmark, Game Servers / VPS
// tabs, search, credit balance, notifications) plus the sidebar + main content
// grid. Both the dashboard and the in-server pages render inside it so the whole
// client shares one cohesive layout/theme.
export default ({ sidebar, variant = 'dashboard', children }: Props) => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const user = useStoreState((state: ApplicationStore) => state.user.data!);
    const credits = typeof user.credits === 'number' ? user.credits : 0;

    const [searchVisible, setSearchVisible] = useState(false);

    // Cmd/Ctrl + "/" opens the global search, matching the previous behaviour.
    useEventListener('keydown', (e: KeyboardEvent) => {
        const tag = ((e.target as HTMLElement)?.tagName || 'input').toLowerCase();
        if (tag !== 'input' && tag !== 'textarea' && !searchVisible && (e.metaKey || e.ctrlKey) && e.key === '/') {
            setSearchVisible(true);
        }
    });

    return (
        <div className={'ruff'}>
            {searchVisible && (
                <SearchModal appear visible={searchVisible} onDismissed={() => setSearchVisible(false)} />
            )}
            <div className={'top'}>
                <Link to={'/'} className={'wm'}>
                    {name}
                </Link>

                <nav className={'tabs'}>
                    <NavLink to={'/'} exact activeClassName={'on'}>
                        <FontAwesomeIcon icon={faServer} /> Game Servers
                    </NavLink>
                    <NavLink to={'/vps'} activeClassName={'on'}>
                        <FontAwesomeIcon icon={faMicrochip} /> VPS
                    </NavLink>
                </nav>

                <button type={'button'} className={'search'} onClick={() => setSearchVisible(true)}>
                    <span className={'si'}>
                        <FontAwesomeIcon icon={faSearch} />
                        <span className={'ph'}>Search servers, nodes...</span>
                        <span className={'k'}>⌘ /</span>
                    </span>
                </button>
                <div className={'tbar-r'}>
                    <div className={'cr'}>
                        <span className={'l'}>Credit</span>
                        <span className={'v'}>£{credits.toFixed(2)}</span>
                        <button className={'add'}>Add</button>
                    </div>
                    <NotificationCenter />
                </div>
            </div>

            <div className={'shell'}>
                <aside className={'side'}>
                    {sidebar}
                    <UserMenu />
                </aside>
                <main className={variant === 'server' ? 'main srv' : 'main'}>{children}</main>
            </div>
        </div>
    );
};
