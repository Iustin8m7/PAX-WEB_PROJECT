/*
Immediately Invoked Function Expression, 
adică o funcție definită și executată imediat.

Toate variabilele și funcțiile din interior există 
într-un scope propriu.
*/

(function () {
    'use strict';

    function ensureContainer(containerOrId) {
        if (!containerOrId) {
            throw new Error('Containerul pentru export nu a fost furnizat.');
        }

        if (typeof containerOrId === 'string') {
            const element = document.getElementById(containerOrId);

            if (!element) {
                throw new Error(`Nu există niciun element cu id-ul "${containerOrId}".`);
            }

            return element;
        }

        if (containerOrId instanceof HTMLElement) {
            return containerOrId;
        }

        throw new Error('Containerul furnizat pentru export este invalid.');
    }

    function findCanvasInContainer(containerOrId) {
        const container = ensureContainer(containerOrId);

        if (container instanceof HTMLCanvasElement) {
            return container;
        }

        const canvas = container.querySelector('canvas');

        if (!canvas) {
            throw new Error('Nu s-a găsit niciun canvas în containerul specificat.');
        }

        return canvas;
    }

    function sanitizeFileNamePart(value) { 
        /// transformă un nume liber într-o formă sigură - pt. numele unui fișier.
        if (value === null || value === undefined) {
            return 'export';
        }

        const normalized = String(value)
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]+/gi, '-')
            .replace(/^-+|-+$/g, '');

        return normalized || 'export';
    }

    function buildTimestamp() {
        ///Generează un timestamp pentru numele fișierului exportat.
        const now = new Date();

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day}_${hours}-${minutes}-${seconds}`;
    }

    function buildFileName(baseName = 'chart_export', extension = 'webp') {
        ///Construiește numele final complet al fișierului.
        const safeBaseName = sanitizeFileNamePart(baseName);
        const safeExtension = sanitizeFileNamePart(extension);

        return `${safeBaseName}_${buildTimestamp()}.${safeExtension}`;
    }

    function triggerDownload(dataUrl, fileName) {
        ///Declanșează efectiv descărcarea unui fișier în browser.
        if (!dataUrl || typeof dataUrl !== 'string') {
            throw new Error('Nu s-a putut genera conținutul imaginii pentru descărcare.');
        }

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = fileName;
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function createExportCanvas(sourceCanvas, backgroundColor = null) {
    const exportCanvas = document.createElement('canvas');
    exportCanvas.width = sourceCanvas.width;
    exportCanvas.height = sourceCanvas.height;

    const context = exportCanvas.getContext('2d');

    if (!context) {
        throw new Error('Nu s-a putut obține contextul 2D pentru export.');
    }

    if (backgroundColor) {
        context.fillStyle = backgroundColor;
        context.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
    }

    context.drawImage(sourceCanvas, 0, 0);

    return exportCanvas;
}

    function exportCanvasToWebP(containerOrId, options = {}) {
    const {
        fileName = 'chart_export',
        quality = 0.95,
        backgroundColor = '#0f172a',
    } = options;

    const sourceCanvas = findCanvasInContainer(containerOrId);

    if (typeof sourceCanvas.toDataURL !== 'function') {
        throw new Error('Canvas-ul selectat nu suportă exportul imaginii.');
    }

    const exportCanvas = createExportCanvas(sourceCanvas, backgroundColor);
    const dataUrl = exportCanvas.toDataURL('image/webp', quality);

    if (!dataUrl || !dataUrl.startsWith('data:image/webp')) {
        throw new Error('Browserul nu a putut genera imaginea în format WebP.');
    }

    const finalFileName = buildFileName(fileName, 'webp');
    triggerDownload(dataUrl, finalFileName);

    return finalFileName;
}

    function isCanvasExportable(containerOrId) {
        ///Verifică dacă un container este exportabil
        try {
            findCanvasInContainer(containerOrId);
            return true;
        } catch (error) {
            return false;
        }
    }

    window.appExportImage = {
        ensureContainer,
        findCanvasInContainer,
        sanitizeFileNamePart,
        buildTimestamp,
        buildFileName,
        triggerDownload,
        exportCanvasToWebP,
        isCanvasExportable,
    };
    ///Toate funcțiile listate aici devin accesibile 
    ///prin:  window.appExportImage.<functie>
})();