import React, { useEffect, useRef, useState } from 'react';
import { useHistory } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCopy, faEllipsisV, faLock, faPlay, faServer, faStop } from '@fortawesome/free-solid-svg-icons';
import { Server } from '@/api/server/getServer';
import getServerResourceUsage, { ServerStats } from '@/api/server/getServerResourceUsage';
import { bytesToString, ip, mbToBytes } from '@/lib/formatters';
import http from '@/api/http';

const PALETTE = ['#7bd06f', '#5b9bff', '#f5934a', '#7289ff', '#c9b8ff', '#40d99a'];

type Timer = ReturnType<typeof setInterval>;

export default ({ server, grid }: { server: Server; grid?: boolean }) => {
    const history = useHistory();
    const interval = useRef<Timer>(null) as React.MutableRefObject<Timer>;
    const [isSuspended, setIsSuspended] = useState(server.status === 'suspended');
    const [stats, setStats] = useState<ServerStats | null>(null);

    const getStats = () =>
        getServerResourceUsage(server.uuid)
            .then((data) => setStats(data))
            .catch((error) => console.error(error));

    useEffect(() => {
        setIsSuspended(stats?.isSuspended || server.status === 'suspended');
    }, [stats?.isSuspended, server.status]);

    useEffect(() => {
        if (isSuspended) return;
        getStats().then(() => {
            interval.current = setInterval(() => getStats(), 30000);
        });
        return () => {
            interval.current && clearInterval(interval.current);
        };
    }, [isSuspended]);

    const iconColor = PALETTE[server.name.charCodeAt(0) % PALETTE.length];
    const alloc = server.allocations.find((a) => a.isDefault);
    const address = alloc ? `${alloc.alias || ip(alloc.ip)}:${alloc.port}` : 'No address';
    const sub = [server.description, server.node].filter(Boolean).join(' · ') || server.node;

    // Status resolution.
    let dotClass = 'dr';
    let label = 'Offline';
    let powerState: 'running' | 'offline' | 'transition' | 'locked' = 'offline';
    if (isSuspended) {
        dotClass = 'dr';
        label = 'Suspended';
        powerState = 'locked';
    } else if (server.status === 'installing') {
        dotClass = 'da';
        label = 'Installing';
        powerState = 'locked';
    } else if (server.isTransferring) {
        dotClass = 'da';
        label = 'Transferring';
        powerState = 'locked';
    } else if (stats) {
        if (stats.status === 'running') {
            dotClass = 'dg';
            label = 'Online';
            powerState = 'running';
        } else if (stats.status === 'starting' || stats.status === 'stopping') {
            dotClass = 'da';
            label = stats.status === 'starting' ? 'Starting' : 'Stopping';
            powerState = 'transition';
        } else {
            dotClass = 'dr';
            label = 'Offline';
            powerState = 'offline';
        }
    } else {
        dotClass = 'da';
        label = 'Connecting';
        powerState = 'transition';
    }

    const isRunning = stats?.status === 'running';
    const cpuLimit = server.limits.cpu;
    const cpu = stats ? stats.cpuUsagePercent : 0;
    const cpuWidth = cpuLimit > 0 ? Math.min(100, (cpu / cpuLimit) * 100) : Math.min(100, cpu);
    const cpuAlarm = cpuLimit > 0 && cpu >= cpuLimit * 0.9;
    const memLimit = server.limits.memory > 0 ? bytesToString(mbToBytes(server.limits.memory)) : '∞';

    const open = () => history.push(`/server/${server.id}`);
    const copyAddress = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        navigator.clipboard?.writeText(address).catch(() => undefined);
    };
    const sendPower = (e: React.MouseEvent, signal: 'start' | 'stop') => {
        e.preventDefault();
        e.stopPropagation();
        http.post(`/api/client/servers/${server.uuid}/power`, { signal })
            .then(() => setTimeout(getStats, 1000))
            .catch((error) => console.error(error));
    };
    const stop = (e: React.MouseEvent) => e.stopPropagation();

    if (grid) {
        return (
            <div className={`cd ${powerState === 'locked' ? 'dim' : ''}`} onClick={open}>
                <div className={'h'}>
                    <div className={'svic'}>
                        <FontAwesomeIcon icon={faServer} style={{ color: iconColor }} />
                    </div>
                    <div style={{ minWidth: 0 }}>
                        <div className={'nm'}>{server.name}</div>
                        <div className={'sub'}>{sub}</div>
                    </div>
                    <span style={{ marginLeft: 'auto' }}>
                        <span className={`dot ${dotClass}`} />
                    </span>
                </div>
                <div className={'kv'}>
                    <span className={'k'}>Connect</span>
                    <span className={'v'}>{address}</span>
                </div>
                <div className={'kv'}>
                    <span className={'k'}>CPU</span>
                    <span className={'v'} style={cpuAlarm ? { color: '#fb6a72' } : undefined}>
                        {isRunning ? `${cpu.toFixed(0)}%` : '—'}
                    </span>
                </div>
                <div className={'kv'}>
                    <span className={'k'}>Memory</span>
                    <span className={'v'}>
                        {isRunning && stats ? `${bytesToString(stats.memoryUsageInBytes)} / ${memLimit}` : '—'}
                    </span>
                </div>
            </div>
        );
    }

    return (
        <div className={`tr2 ${powerState === 'locked' ? 'dim' : ''}`} onClick={open}>
            <div className={'sv'}>
                <div className={'svic'}>
                    <FontAwesomeIcon icon={faServer} style={{ color: iconColor }} />
                </div>
                <div style={{ minWidth: 0 }}>
                    <div className={'nm'}>{server.name}</div>
                    <div className={'sub'}>{sub}</div>
                </div>
            </div>
            <div className={'st'}>
                <span className={`dot ${dotClass}`} />
                {label}
            </div>
            <div className={'ad'}>
                <span>{address}</span>
                <button onClick={copyAddress} aria-label={'Copy address'}>
                    <FontAwesomeIcon icon={faCopy} />
                </button>
            </div>
            <div className={'load'}>
                {isRunning && stats ? (
                    <>
                        <div className={'l1'}>
                            <div className={'bar'}>
                                <div className={`f ${cpuAlarm ? 'hi' : ''}`} style={{ width: `${cpuWidth}%` }} />
                            </div>
                            <span className={`p ${cpuAlarm ? 'hi' : ''}`}>{cpu.toFixed(0)}%</span>
                        </div>
                        <div className={'mem'}>
                            {bytesToString(stats.memoryUsageInBytes)} / {memLimit} memory
                        </div>
                    </>
                ) : (
                    <span className={'na'}>{isSuspended ? 'Suspended' : 'Server is offline'}</span>
                )}
            </div>
            {powerState === 'locked' ? (
                <button className={'pw'} disabled onClick={stop}>
                    <FontAwesomeIcon icon={faLock} /> Locked
                </button>
            ) : powerState === 'offline' ? (
                <button className={'pw start'} onClick={(e) => sendPower(e, 'start')}>
                    <FontAwesomeIcon icon={faPlay} /> Start
                </button>
            ) : (
                <button className={'pw stop'} onClick={(e) => sendPower(e, 'stop')}>
                    <FontAwesomeIcon icon={faStop} /> Stop
                </button>
            )}
            <button className={'kb'} onClick={stop} aria-label={'More'}>
                <FontAwesomeIcon icon={faEllipsisV} />
            </button>
        </div>
    );
};
