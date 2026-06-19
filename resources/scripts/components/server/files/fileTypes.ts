import {
    faFile,
    faFileAlt,
    faFileArchive,
    faFileAudio,
    faFileCode,
    faFileImage,
    faFileImport,
    faFilePdf,
    faFileVideo,
    faFolder,
    IconDefinition,
} from '@fortawesome/free-solid-svg-icons';
import { FileObject } from '@/api/server/files/loadDirectory';

export type FileCategory =
    | 'directory'
    | 'symlink'
    | 'image'
    | 'audio'
    | 'video'
    | 'pdf'
    | 'archive'
    | 'code'
    | 'text'
    | 'binary';

const IMAGE_EXT = new Set(['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg', 'ico', 'avif']);
const AUDIO_EXT = new Set(['mp3', 'ogg', 'wav', 'flac', 'm4a', 'aac']);
const VIDEO_EXT = new Set(['mp4', 'webm', 'ogv', 'mov', 'mkv']);
const ARCHIVE_EXT = new Set(['zip', 'tar', 'gz', 'tgz', 'rar', '7z', 'bz2', 'xz']);
const CODE_EXT = new Set([
    'js',
    'jsx',
    'ts',
    'tsx',
    'json',
    'yml',
    'yaml',
    'toml',
    'ini',
    'cfg',
    'conf',
    'properties',
    'sh',
    'bash',
    'php',
    'py',
    'rb',
    'rs',
    'go',
    'java',
    'kt',
    'c',
    'h',
    'cpp',
    'hpp',
    'cs',
    'lua',
    'sql',
    'html',
    'htm',
    'css',
    'scss',
    'less',
    'xml',
    'md',
    'markdown',
    'env',
    'pl',
    'swift',
    'dart',
    'vue',
    'gradle',
]);
const TEXT_EXT = new Set(['txt', 'log', 'csv', 'text', 'me', 'gitignore', 'gitattributes', 'editorconfig']);

export const extensionOf = (name: string): string => {
    const dot = name.lastIndexOf('.');
    return dot > -1 ? name.slice(dot + 1).toLowerCase() : '';
};

/**
 * Resolve a file's category using its mimetype when available, falling back to
 * its extension. Used in the file listing (which has the full FileObject).
 */
export const categoryFor = (file: FileObject): FileCategory => {
    if (!file.isFile) return 'directory';
    if (file.isSymlink) return 'symlink';

    const mime = (file.mimetype || '').toLowerCase();
    if (mime.startsWith('image/')) return 'image';
    if (mime.startsWith('audio/')) return 'audio';
    if (mime.startsWith('video/')) return 'video';
    if (mime === 'application/pdf') return 'pdf';
    if (file.isArchiveType()) return 'archive';

    return categoryForName(file.name, mime.startsWith('text/'));
};

/**
 * Resolve a category from a filename alone (used by the preview screen, which
 * only receives the path via the URL hash).
 */
export const categoryForName = (name: string, assumeText = false): FileCategory => {
    const ext = extensionOf(name);
    if (IMAGE_EXT.has(ext)) return 'image';
    if (AUDIO_EXT.has(ext)) return 'audio';
    if (VIDEO_EXT.has(ext)) return 'video';
    if (ext === 'pdf') return 'pdf';
    if (ARCHIVE_EXT.has(ext)) return 'archive';
    if (CODE_EXT.has(ext)) return 'code';
    if (TEXT_EXT.has(ext) || assumeText) return 'text';
    return 'binary';
};

export const iconFor = (category: FileCategory): IconDefinition =>
    ({
        directory: faFolder,
        symlink: faFileImport,
        image: faFileImage,
        audio: faFileAudio,
        video: faFileVideo,
        pdf: faFilePdf,
        archive: faFileArchive,
        code: faFileCode,
        text: faFileAlt,
        binary: faFile,
    }[category]);

/** Categories rendered as embedded media in the preview screen. */
export const isMedia = (category: FileCategory): boolean =>
    category === 'image' || category === 'audio' || category === 'video' || category === 'pdf';

/** Categories the preview screen can render at all (media or read-only text). */
export const isPreviewable = (category: FileCategory): boolean =>
    isMedia(category) || category === 'code' || category === 'text';
