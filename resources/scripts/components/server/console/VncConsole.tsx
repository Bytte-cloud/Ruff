import React, { useCallback, useEffect, useRef, useState } from 'react';
import RFB from '@novnc/novnc/core/rfb';
import { ServerContext } from '@/state/server';
import getWebsocketToken from '@/api/server/getWebsocketToken';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faDesktop, faSyncAlt } from '@fortawesome/free-solid-svg-icons';

type Status = 'connecting' | 'connected' | 'disconnected';

// VncConsole renders a noVNC graphical console for VM (QEMU) servers. It fetches
// the same short-lived websocket token the text console uses and connects to the
// daemon's /vnc proxy endpoint (which tunnels RFB to the VM's VNC port).
const VncConsole = () => {
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const screenRef = useRef<HTMLDivElement>(null);
    const rfbRef = useRef<RFB | null>(null);
    const [status, setStatus] = useState<Status>('connecting');
    const [error, setError] = useState<string | null>(null);

    const cleanup = () => {
        if (rfbRef.current) {
            try {
                rfbRef.current.disconnect();
            } catch (e) {
                /* already gone */
            }
            rfbRef.current = null;
        }
    };

    const connect = useCallback(async () => {
        if (!screenRef.current) return;
        cleanup();
        setStatus('connecting');
        setError(null);

        try {
            const { token, socket } = await getWebsocketToken(uuid);
            const url = `${socket.replace(/\/ws$/, '/vnc')}?token=${encodeURIComponent(token)}`;

            const rfb = new RFB(screenRef.current, url, { wsProtocols: ['binary'] });
            rfb.scaleViewport = true;
            rfb.background = '#08080d';
            rfb.focusOnClick = true;
            rfb.addEventListener('connect', () => setStatus('connected'));
            rfb.addEventListener('disconnect', (e: Event) => {
                setStatus('disconnected');
                if (!(e as CustomEvent).detail?.clean) {
                    setError('The graphical console connection was lost.');
                }
            });
            rfb.addEventListener('securityfailure', () => {
                setStatus('disconnected');
                setError('The graphical console rejected the connection.');
            });
            rfbRef.current = rfb;
        } catch (e) {
            setStatus('disconnected');
            setError('Could not open the graphical console. The VM may be offline.');
        }
    }, [uuid]);

    useEffect(() => {
        connect();
        return cleanup;
    }, [connect]);

    return (
        <div className={'w-full flex flex-col bg-gray-900 rounded-lg overflow-hidden border border-gray-700'}>
            <div className={'flex items-center justify-between px-4 py-2 bg-gray-700 text-sm'}>
                <span className={'flex items-center font-semibold text-gray-200'}>
                    <FontAwesomeIcon icon={faDesktop} className={'mr-2'} />
                    Graphical console
                    <span
                        className={`ml-3 inline-block w-2 h-2 rounded-full ${
                            status === 'connected'
                                ? 'bg-green-500'
                                : status === 'connecting'
                                ? 'bg-yellow-500'
                                : 'bg-red-500'
                        }`}
                    />
                    <span className={'ml-2 text-xs text-gray-400 capitalize'}>{status}</span>
                </span>
                <button
                    onClick={() => connect()}
                    className={'flex items-center text-xs font-semibold text-gray-300 hover:text-gray-100'}
                >
                    <FontAwesomeIcon icon={faSyncAlt} className={'mr-1.5'} /> Reconnect
                </button>
            </div>
            <div ref={screenRef} style={{ height: '70vh', minHeight: 360, background: '#08080d' }} />
            {error && (
                <div className={'px-4 py-2 text-sm text-red-400 bg-gray-700 border-t border-gray-600'}>{error}</div>
            )}
        </div>
    );
};

export default VncConsole;
