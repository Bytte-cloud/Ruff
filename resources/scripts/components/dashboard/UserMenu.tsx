import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faChevronUp, faSignOutAlt, faUser } from '@fortawesome/free-solid-svg-icons';
import http from '@/api/http';

// The account avatar + dropdown pinned to the bottom of the sidebar. Shared by
// every shell variant (dashboard and in-server) so the chrome stays identical.
export default () => {
    const user = useStoreState((state: ApplicationStore) => state.user.data!);
    const [menuOpen, setMenuOpen] = useState(false);

    const initials = (user.username || user.email || '?').substring(0, 2).toUpperCase();

    const onLogout = () => {
        http.post('/auth/logout').finally(() => {
            // @ts-expect-error this is valid
            window.location = '/auth/login';
        });
    };

    return (
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
    );
};
