import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faChevronUp,
    faKey,
    faSearch,
    faServer,
    faShieldAlt,
    faSignOutAlt,
    faTerminal,
    faUser,
    faWaveSquare,
} from '@fortawesome/free-solid-svg-icons';
import http from '@/api/http';
import NotificationCenter from '@/components/dashboard/NotificationCenter';

export default ({ children }: { children: React.ReactNode }) => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const user = useStoreState((state: ApplicationStore) => state.user.data!);
    const [menuOpen, setMenuOpen] = useState(false);

    const initials = (user.username || user.email || '?').substring(0, 2).toUpperCase();
    const credits = typeof user.credits === 'number' ? user.credits : 0;

    const onLogout = () => {
        http.post('/auth/logout').finally(() => {
            // @ts-expect-error this is valid
            window.location = '/auth/login';
        });
    };

    return (
        <div className={'ruff'}>
            <div className={'top'}>
                <Link to={'/'} className={'wm'}>
                    {name}
                </Link>
                <div className={'search'}>
                    <FontAwesomeIcon icon={faSearch} />
                    <input placeholder={'Search servers, nodes...'} />
                    <span className={'k'}>⌘ /</span>
                </div>
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
                    <nav className={'nav'}>
                        <div className={'sec'}>Manage</div>
                        <NavLink to={'/'} exact activeClassName={'on'}>
                            <FontAwesomeIcon icon={faServer} /> Servers
                        </NavLink>
                        <NavLink to={'/account/activity'} activeClassName={'on'}>
                            <FontAwesomeIcon icon={faWaveSquare} /> Activity
                        </NavLink>

                        <div className={'sec'}>Account</div>
                        <NavLink to={'/account'} exact activeClassName={'on'}>
                            <FontAwesomeIcon icon={faUser} /> Profile
                        </NavLink>
                        <NavLink to={'/account/api'} activeClassName={'on'}>
                            <FontAwesomeIcon icon={faKey} /> API Credentials
                        </NavLink>
                        <NavLink to={'/account/ssh'} activeClassName={'on'}>
                            <FontAwesomeIcon icon={faTerminal} /> SSH Keys
                        </NavLink>

                        {user.rootAdmin && (
                            <>
                                <div className={'sec'}>System</div>
                                <a href={'/admin'} rel={'noreferrer'}>
                                    <FontAwesomeIcon icon={faShieldAlt} /> Admin Panel
                                </a>
                            </>
                        )}
                    </nav>

                    <div className={'su'} style={{ position: 'relative' }} onClick={() => setMenuOpen((v) => !v)}>
                        <div className={'a'}>{initials}</div>
                        <div className={'m'}>
                            <div className={'n'}>{user.username}</div>
                            <div className={'e'}>{user.email}</div>
                        </div>
                        <FontAwesomeIcon icon={faChevronUp} className={'ch'} />
                        {menuOpen && (
                            <div
                                style={{
                                    position: 'absolute',
                                    bottom: 'calc(100% + 8px)',
                                    left: 12,
                                    right: 12,
                                    background: 'hsl(var(--c-gray-700))',
                                    border: '1px solid hsl(var(--c-gray-500))',
                                    borderRadius: 10,
                                    padding: 6,
                                    boxShadow: '0 12px 28px -16px hsl(var(--c-black) / .6)',
                                    zIndex: 40,
                                }}
                            >
                                <Link
                                    to={'/account'}
                                    style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: 10,
                                        padding: '9px 11px',
                                        borderRadius: 8,
                                        color: 'hsl(var(--c-gray-300))',
                                        fontSize: 13.5,
                                        fontWeight: 500,
                                    }}
                                    onClick={() => setMenuOpen(false)}
                                >
                                    <FontAwesomeIcon icon={faUser} /> Account settings
                                </Link>
                                <button
                                    onClick={onLogout}
                                    style={{
                                        display: 'flex',
                                        width: '100%',
                                        alignItems: 'center',
                                        gap: 10,
                                        padding: '9px 11px',
                                        borderRadius: 8,
                                        color: 'hsl(var(--c-danger-500))',
                                        fontSize: 13.5,
                                        fontWeight: 500,
                                        background: 'none',
                                        border: 0,
                                        cursor: 'pointer',
                                        fontFamily: 'inherit',
                                    }}
                                >
                                    <FontAwesomeIcon icon={faSignOutAlt} /> Sign out
                                </button>
                            </div>
                        )}
                    </div>
                </aside>

                <main className={'main'}>{children}</main>
            </div>
        </div>
    );
};
