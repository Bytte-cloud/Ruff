import React from 'react';
import { NavLink } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faKey,
    faMicrochip,
    faServer,
    faShieldAlt,
    faTerminal,
    faUser,
    faWaveSquare,
} from '@fortawesome/free-solid-svg-icons';

// Sidebar navigation for the dashboard + account pages.
export default () => {
    const user = useStoreState((state: ApplicationStore) => state.user.data!);

    return (
        <nav className={'nav'}>
            <div className={'sec'}>Manage</div>
            <NavLink to={'/'} exact activeClassName={'on'}>
                <FontAwesomeIcon icon={faServer} /> Game Servers
            </NavLink>
            <NavLink to={'/vps'} activeClassName={'on'}>
                <FontAwesomeIcon icon={faMicrochip} /> VPS
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
    );
};
