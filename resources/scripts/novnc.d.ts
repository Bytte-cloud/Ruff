// noVNC does not ship TypeScript types. We only use the RFB default export, so a
// loose declaration is sufficient.
declare module '@novnc/novnc/core/rfb' {
    export default class RFB extends EventTarget {
        constructor(target: HTMLElement, url: string, options?: Record<string, unknown>);
        scaleViewport: boolean;
        resizeSession: boolean;
        background: string;
        viewOnly: boolean;
        focusOnClick: boolean;
        showDotCursor: boolean;
        disconnect(): void;
        focus(): void;
        sendCtrlAltDel(): void;
    }
}
