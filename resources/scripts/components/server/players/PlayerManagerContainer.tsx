import React, { useCallback, useEffect, useState } from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSync, faUserSlash, faGavel } from '@fortawesome/free-solid-svg-icons';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import GreyRowBox from '@/components/elements/GreyRowBox';
import Button from '@/components/elements/Button';
import Can from '@/components/elements/Can';
import Spinner from '@/components/elements/Spinner';
import { ServerContext } from '@/state/server';
import useWebsocketEvent from '@/plugins/useWebsocketEvent';
import { SocketEvent } from '@/components/server/events';

interface PlayerList {
    count: number;
    max: number;
    names: string[];
}

// Strip the ANSI colour codes and legacy Minecraft section signs the daemon
// streams alongside console output so the line can be pattern-matched. The ANSI
// pattern is built from the ESC char code to avoid a literal control character.
const ANSI = new RegExp(String.fromCharCode(27) + '\\[[0-9;]*m', 'g');
const clean = (line: string): string => line.replace(ANSI, '').replace(/§./g, '');

/**
 * Parse the response to the vanilla/Spigot `list` command, e.g.
 *   "There are 2 of a max of 20 players online: Steve, Alex"
 *   "There are 1/20 players online: Steve"
 */
const parseList = (line: string): PlayerList | null => {
    const text = clean(line);
    const match =
        text.match(/There are (\d+) of a max(?: of)? (\d+) players online:?\s*(.*)$/i) ||
        text.match(/There are (\d+)\/(\d+) players online:?\s*(.*)$/i);

    if (!match) {
        return null;
    }

    const names = (match[3] || '')
        .split(',')
        .map((n) => n.replace(/\(.*?\)/g, '').trim())
        .filter(Boolean);

    return { count: parseInt(match[1], 10), max: parseInt(match[2], 10), names };
};

export default () => {
    const instance = ServerContext.useStoreState((state) => state.socket.instance);
    const connected = ServerContext.useStoreState((state) => state.socket.connected);
    const status = ServerContext.useStoreState((state) => state.status.value);

    const [list, setList] = useState<PlayerList | null>(null);
    const [waiting, setWaiting] = useState(true);

    const isRunning = status === 'running';

    const query = useCallback(() => {
        if (instance && connected && isRunning) {
            instance.send('send command', 'list');
        }
    }, [instance, connected, isRunning]);

    // Capture the `list` reply as it streams back through the console socket.
    useWebsocketEvent(SocketEvent.CONSOLE_OUTPUT, (line) => {
        const parsed = parseList(line);
        if (parsed) {
            setList(parsed);
            setWaiting(false);
        }
    });

    useEffect(() => {
        if (!isRunning) {
            setList(null);
            setWaiting(false);
            return;
        }

        setWaiting(true);
        query();
        const interval = setInterval(query, 10000);
        // If nothing comes back shortly, stop showing the spinner.
        const timeout = setTimeout(() => setWaiting(false), 4000);

        return () => {
            clearInterval(interval);
            clearTimeout(timeout);
        };
    }, [query, isRunning]);

    const sendCommand = (command: string) => {
        instance && instance.send('send command', command);
        setTimeout(query, 800);
    };

    const kick = (name: string) => {
        if (window.confirm(`Kick ${name} from the server?`)) {
            sendCommand(`kick ${name}`);
        }
    };

    const ban = (name: string) => {
        if (window.confirm(`Ban ${name}? They will not be able to reconnect until unbanned.`)) {
            sendCommand(`ban ${name}`);
        }
    };

    return (
        <ServerContentBlock title={'Players'}>
            <div css={tw`flex items-end justify-between mb-4`}>
                <div>
                    <p css={tw`text-sm text-neutral-300`}>
                        {isRunning && list ? `${list.count} of ${list.max} players online` : 'Player list'}
                    </p>
                    <p css={tw`text-xs text-neutral-400 mt-1`}>
                        Queries the Minecraft <code css={tw`font-mono`}>list</code> command over the console.
                    </p>
                </div>
                <Can action={'control.console'}>
                    <Button isSecondary onClick={query} disabled={!isRunning}>
                        <FontAwesomeIcon icon={faSync} css={tw`mr-2`} /> Refresh
                    </Button>
                </Can>
            </div>

            {!isRunning ? (
                <GreyRowBox css={tw`justify-center text-neutral-300`}>
                    The server is offline. Start it to see who&apos;s online.
                </GreyRowBox>
            ) : waiting && !list ? (
                <div css={tw`py-10`}>
                    <Spinner centered />
                </div>
            ) : !list || list.names.length === 0 ? (
                <GreyRowBox css={tw`justify-center text-neutral-300`}>No players are currently online.</GreyRowBox>
            ) : (
                list.names.map((name) => (
                    <GreyRowBox key={name} css={tw`mb-2`}>
                        <img
                            src={`https://minotar.net/helm/${encodeURIComponent(name)}/40.png`}
                            alt={name}
                            css={tw`w-10 h-10 rounded`}
                        />
                        <div css={tw`ml-4 flex-1`}>
                            <p css={tw`text-neutral-100 font-medium`}>{name}</p>
                        </div>
                        <Can action={'control.console'}>
                            <div css={tw`flex items-center`}>
                                <Button size={'xsmall'} isSecondary onClick={() => kick(name)} css={tw`mr-2`}>
                                    <FontAwesomeIcon icon={faUserSlash} css={tw`mr-1`} /> Kick
                                </Button>
                                <Button size={'xsmall'} color={'red'} isSecondary onClick={() => ban(name)}>
                                    <FontAwesomeIcon icon={faGavel} css={tw`mr-1`} /> Ban
                                </Button>
                            </div>
                        </Can>
                    </GreyRowBox>
                ))
            )}
        </ServerContentBlock>
    );
};
