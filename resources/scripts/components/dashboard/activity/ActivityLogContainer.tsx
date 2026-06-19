import React, { useEffect, useState } from 'react';
import { ActivityLogFilters, useActivityLogs } from '@/api/account/activity';
import { useFlashKey } from '@/plugins/useFlash';
import FlashMessageRender from '@/components/FlashMessageRender';
import PaginationFooter from '@/components/elements/table/PaginationFooter';
import { DesktopComputerIcon, XCircleIcon } from '@heroicons/react/solid';
import Spinner from '@/components/elements/Spinner';
import ActivityLogEntry from '@/components/elements/activity/ActivityLogEntry';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import useLocationHash from '@/plugins/useLocationHash';

export default () => {
    const { hash } = useLocationHash();
    const { clearAndAddHttpError } = useFlashKey('account');
    const [filters, setFilters] = useState<ActivityLogFilters>({ page: 1, sorts: { timestamp: -1 } });
    const { data, isValidating, error } = useActivityLogs(filters, {
        revalidateOnMount: true,
        revalidateOnFocus: false,
    });

    const hasFilters = !!(filters.filters?.event || filters.filters?.ip);
    const total = data?.pagination.total ?? 0;

    useEffect(() => {
        document.title = 'Account Activity';
    }, []);

    useEffect(() => {
        setFilters((value) => ({ ...value, filters: { ip: hash.ip, event: hash.event } }));
    }, [hash]);

    useEffect(() => {
        clearAndAddHttpError(error);
    }, [error]);

    return (
        <>
            <FlashMessageRender byKey={'account'} />

            <div className={'ph'}>
                <h1>Account Activity</h1>
                <span className={'c'}>
                    {total} {total === 1 ? 'event' : 'events'}
                </span>
                {hasFilters && (
                    <div className={'r'}>
                        <button
                            onClick={() => setFilters((value) => ({ ...value, filters: {} }))}
                            className={
                                'inline-flex items-center gap-2 text-sm text-neutral-400 hover:text-neutral-100 transition-colors'
                            }
                            style={{ background: 'none', border: 0, cursor: 'pointer', fontFamily: 'inherit' }}
                        >
                            Clear filters <XCircleIcon className={'w-4 h-4'} />
                        </button>
                    </div>
                )}
            </div>

            {!data && isValidating ? (
                <div className={'spin-wrap'}>
                    <Spinner size={'large'} />
                </div>
            ) : !data || data.items.length === 0 ? (
                <p className={'empty'}>
                    {hasFilters
                        ? 'No activity matches the current filters.'
                        : 'No account activity has been recorded yet.'}
                </p>
            ) : (
                <div className={'tbl'}>
                    {data.items.map((activity) => (
                        <ActivityLogEntry key={activity.id} activity={activity}>
                            {typeof activity.properties.useragent === 'string' && (
                                <Tooltip content={activity.properties.useragent} placement={'top'}>
                                    <span>
                                        <DesktopComputerIcon />
                                    </span>
                                </Tooltip>
                            )}
                        </ActivityLogEntry>
                    ))}
                </div>
            )}

            {data && data.items.length > 0 && (
                <PaginationFooter
                    pagination={data.pagination}
                    onPageSelect={(page) => setFilters((value) => ({ ...value, page }))}
                />
            )}
        </>
    );
};
