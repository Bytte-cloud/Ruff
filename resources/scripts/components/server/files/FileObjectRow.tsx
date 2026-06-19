import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { encodePathSegments } from '@/helpers';
import { differenceInHours, format, formatDistanceToNow } from 'date-fns';
import React, { memo } from 'react';
import { FileObject } from '@/api/server/files/loadDirectory';
import FileDropdownMenu from '@/components/server/files/FileDropdownMenu';
import { ServerContext } from '@/state/server';
import { NavLink, useRouteMatch } from 'react-router-dom';
import tw from 'twin.macro';
import isEqual from 'react-fast-compare';
import SelectFileCheckbox from '@/components/server/files/SelectFileCheckbox';
import { usePermissions } from '@/plugins/usePermissions';
import { join } from 'path';
import { bytesToString } from '@/lib/formatters';
import { categoryFor, iconFor, isMedia } from '@/components/server/files/fileTypes';
import styles from './style.module.css';

const Clickable: React.FC<{ file: FileObject }> = memo(({ file, children }) => {
    const [canRead] = usePermissions(['file.read']);
    const [canReadContents] = usePermissions(['file.read-content']);
    const directory = ServerContext.useStoreState((state) => state.files.directory);

    const match = useRouteMatch();

    const hash = encodePathSegments(join(directory, file.name));
    const category = categoryFor(file);

    // Directories navigate; media open the preview; editable files open the
    // editor. Everything else (non-readable binaries) is not clickable.
    let target: string | null = null;
    if (!file.isFile) {
        target = canRead ? `${match.url}#${hash}` : null;
    } else if (canReadContents) {
        if (isMedia(category)) {
            target = `${match.url}/view#${hash}`;
        } else if (file.isEditable()) {
            target = `${match.url}/edit#${hash}`;
        }
    }

    return target === null ? (
        <div className={styles.details}>{children}</div>
    ) : (
        <NavLink className={styles.details} to={target}>
            {children}
        </NavLink>
    );
}, isEqual);

const FileObjectRow = ({ file }: { file: FileObject }) => (
    <div
        className={styles.file_row}
        key={file.name}
        onContextMenu={(e) => {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent(`Ruff:files:ctx:${file.key}`, { detail: e.clientX }));
        }}
    >
        <SelectFileCheckbox name={file.name} />
        <Clickable file={file}>
            <div css={tw`flex-none text-neutral-400 ml-6 mr-4 text-lg pl-3`}>
                <FontAwesomeIcon icon={iconFor(categoryFor(file))} />
            </div>
            <div css={tw`flex-1 truncate`}>{file.name}</div>
            {file.isFile && <div css={tw`w-1/6 text-right mr-4 hidden sm:block`}>{bytesToString(file.size)}</div>}
            <div css={tw`w-1/5 text-right mr-4 hidden md:block`} title={file.modifiedAt.toString()}>
                {Math.abs(differenceInHours(file.modifiedAt, new Date())) > 48
                    ? format(file.modifiedAt, 'MMM do, yyyy h:mma')
                    : formatDistanceToNow(file.modifiedAt, { addSuffix: true })}
            </div>
        </Clickable>
        <FileDropdownMenu file={file} />
    </div>
);

export default memo(FileObjectRow, (prevProps, nextProps) => {
    /* eslint-disable @typescript-eslint/no-unused-vars */
    const { isArchiveType, isEditable, ...prevFile } = prevProps.file;
    const { isArchiveType: nextIsArchiveType, isEditable: nextIsEditable, ...nextFile } = nextProps.file;
    /* eslint-enable @typescript-eslint/no-unused-vars */

    return isEqual(prevFile, nextFile);
});
