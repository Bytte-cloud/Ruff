import React, { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faBell,
    faCreditCard,
    faInfoCircle,
    faShieldAlt,
    faWrench,
    IconDefinition,
} from '@fortawesome/free-solid-svg-icons';
import { useHistory } from 'react-router-dom';
import { formatDistanceToNowStrict } from 'date-fns';
import useSWR from 'swr';
import {
    AppNotification,
    NotificationCategory,
    NotificationsResult,
    getNotifications,
    markAllNotificationsRead,
    markNotificationRead,
} from '@/api/account/notifications';

const ICONS: Record<NotificationCategory, IconDefinition> = {
    event: faShieldAlt,
    billing: faCreditCard,
    maintenance: faWrench,
    system: faInfoCircle,
};

export default () => {
    const history = useHistory();
    const [open, setOpen] = useState(false);

    const { data, mutate } = useSWR<NotificationsResult>('/api/client/account/notifications', getNotifications, {
        revalidateOnFocus: true,
        refreshInterval: 60000,
    });

    const items = data?.items || [];
    const unread = data?.unreadCount || 0;

    const refresh = () => mutate();

    const onItemClick = (n: AppNotification) => {
        if (!n.read) {
            markNotificationRead(n.id).then(refresh);
        }
        if (n.actionUrl) {
            setOpen(false);
            if (n.actionUrl.startsWith('/')) {
                history.push(n.actionUrl);
            } else {
                window.open(n.actionUrl, '_blank');
            }
        }
    };

    const onMarkAll = () => {
        markAllNotificationsRead().then(refresh);
    };

    return (
        <div style={{ position: 'relative' }}>
            <button className={'ibtn'} aria-label={'Notifications'} onClick={() => setOpen((v) => !v)}>
                <FontAwesomeIcon icon={faBell} />
                {unread > 0 && <span className={'d'} />}
            </button>

            {open && (
                <>
                    <div className={'noti-bd'} onClick={() => setOpen(false)} />
                    <div className={'noti'}>
                        <div className={'noti-h'}>
                            <span>Notifications {unread > 0 && <b>{unread}</b>}</span>
                            {unread > 0 && (
                                <button onClick={onMarkAll} className={'noti-mark'}>
                                    Mark all read
                                </button>
                            )}
                        </div>

                        <div className={'noti-list'}>
                            {items.length === 0 ? (
                                <p className={'noti-empty'}>You&apos;re all caught up.</p>
                            ) : (
                                items.map((n) => (
                                    <button
                                        key={n.id}
                                        className={`noti-i ${n.read ? '' : 'unread'} ${n.actionUrl ? 'link' : ''}`}
                                        onClick={() => onItemClick(n)}
                                    >
                                        <span className={`noti-ic ${n.category}`}>
                                            <FontAwesomeIcon icon={ICONS[n.category] || faInfoCircle} />
                                        </span>
                                        <span className={'noti-b'}>
                                            <span className={'noti-t'}>{n.title}</span>
                                            <span className={'noti-m'}>{n.message}</span>
                                            <span className={'noti-time'}>
                                                {formatDistanceToNowStrict(n.createdAt, { addSuffix: true })}
                                            </span>
                                        </span>
                                        {!n.read && <span className={'noti-dot'} />}
                                    </button>
                                ))
                            )}
                        </div>
                    </div>
                </>
            )}
        </div>
    );
};
