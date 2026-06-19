import React, { useEffect, useState } from 'react';
import { useHistory, useLocation } from 'react-router';
import { dirname } from 'path';
import tw from 'twin.macro';
import getFileContents from '@/api/server/files/getFileContents';
import getFileDownloadUrl from '@/api/server/files/getFileDownloadUrl';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import { encodePathSegments, hashToPath } from '@/helpers';
import { categoryForName, isMedia } from '@/components/server/files/fileTypes';
import PageContentBlock from '@/components/elements/PageContentBlock';
import FlashMessageRender from '@/components/FlashMessageRender';
import FileManagerBreadcrumbs from '@/components/server/files/FileManagerBreadcrumbs';
import { ServerError } from '@/components/elements/ScreenBlock';
import ErrorBoundary from '@/components/elements/ErrorBoundary';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import Button, { LinkButton } from '@/components/elements/Button';
import CodemirrorEditor from '@/components/elements/CodemirrorEditor';

export default () => {
    const history = useHistory();
    const { hash } = useLocation();

    const path = hashToPath(hash);
    const name = path.split('/').filter(Boolean).pop() || path;
    const category = categoryForName(name);

    const id = ServerContext.useStoreState((state) => state.server.data!.id);
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const setDirectory = ServerContext.useStoreActions((actions) => actions.files.setDirectory);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [content, setContent] = useState('');
    const [url, setUrl] = useState('');
    const [mode, setMode] = useState('text/plain');

    const editable = category === 'code' || category === 'text';

    useEffect(() => {
        setError('');
        setLoading(true);
        setDirectory(dirname(path));

        const request = isMedia(category)
            ? getFileDownloadUrl(uuid, path).then(setUrl)
            : editable
            ? getFileContents(uuid, path).then(setContent)
            : getFileDownloadUrl(uuid, path).then(setUrl);

        request.catch((err) => setError(httpErrorToHuman(err))).then(() => setLoading(false));
    }, [uuid, hash]);

    const download = () => {
        getFileDownloadUrl(uuid, path).then((u) => {
            // @ts-expect-error this is valid
            window.location = u;
        });
    };

    if (error) {
        return <ServerError message={error} onBack={() => history.goBack()} />;
    }

    return (
        <PageContentBlock>
            <FlashMessageRender byKey={'files:view'} css={tw`mb-4`} />
            <ErrorBoundary>
                <div css={tw`mb-4 flex flex-wrap items-center justify-between gap-y-2`}>
                    <FileManagerBreadcrumbs withinFileEditor />
                    <div css={tw`flex items-center`}>
                        {editable && (
                            <LinkButton
                                href={`/server/${id}/files/edit#/${encodePathSegments(path)}`}
                                onClick={(e) => {
                                    e.preventDefault();
                                    history.push(`/server/${id}/files/edit#/${encodePathSegments(path)}`);
                                }}
                                isSecondary
                                css={tw`mr-2`}
                            >
                                Edit
                            </LinkButton>
                        )}
                        <Button onClick={download}>Download</Button>
                    </div>
                </div>
            </ErrorBoundary>

            <div css={tw`relative rounded bg-neutral-700 border border-neutral-500 p-4`}>
                <SpinnerOverlay visible={loading} />

                {category === 'image' && (
                    <div css={tw`flex justify-center`}>
                        <img src={url} css={tw`max-w-full max-h-[70vh] rounded`} alt={name} />
                    </div>
                )}

                {category === 'audio' && (
                    <div css={tw`flex justify-center py-8`}>
                        <audio src={url} controls css={tw`w-full max-w-xl`} />
                    </div>
                )}

                {category === 'video' && (
                    <div css={tw`flex justify-center`}>
                        <video src={url} controls css={tw`max-w-full max-h-[70vh] rounded`} />
                    </div>
                )}

                {category === 'pdf' && url && (
                    <iframe title={name} src={url} css={tw`w-full rounded bg-white`} style={{ height: '78vh' }} />
                )}

                {editable && (
                    <CodemirrorEditor
                        readOnly
                        mode={mode}
                        filename={name}
                        initialContent={content}
                        onModeChanged={setMode}
                        fetchContent={() => undefined}
                        onContentSaved={() => undefined}
                    />
                )}

                {!isMedia(category) && !editable && (
                    <div css={tw`py-12 text-center`}>
                        <p css={tw`text-neutral-300`}>This file type can&apos;t be previewed.</p>
                        <p css={tw`text-neutral-400 text-sm mt-1`}>Use the Download button to grab a copy.</p>
                    </div>
                )}
            </div>
        </PageContentBlock>
    );
};
