(function () {
    'use strict';

    const DEFAULT_TIMEOUT_MS = 12000;

    function buildApiUrl(path, queryParams = {}) {
        const baseUrl = window.APP_API_BASE_URL || 'api';
        const normalizedBase = String(baseUrl).replace(/\/+$/, '');
        const normalizedPath = String(path || '').replace(/^\/+/, '');

        const url = new URL(`${normalizedBase}/${normalizedPath}`, window.location.origin);

        Object.entries(queryParams).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') {
                return;
            }

            url.searchParams.set(key, String(value));
        });

        return url.toString();
    }

    function createTimeoutSignal(timeoutMs) {
        const controller = new AbortController();
        const timeoutId = window.setTimeout(() => {
            controller.abort();
        }, timeoutMs);

        return {
            signal: controller.signal,
            clear() {
                window.clearTimeout(timeoutId);
            },
        };
    }

    async function parseJsonSafely(response) {
        const contentType = response.headers.get('content-type') || '';

        if (!contentType.includes('application/json')) {
            throw new Error('Răspunsul serverului nu este în format JSON.');
        }

        try {
            return await response.json();
        } catch (error) {
            throw new Error('Răspuns JSON invalid primit de la server.');
        }
    }

    function extractErrorMessage(response, payload) {
        if (payload && typeof payload === 'object') {
            if (typeof payload.message === 'string' && payload.message.trim() !== '') {
                return payload.message;
            }

            if (typeof payload.error === 'string' && payload.error.trim() !== '') {
                return payload.error;
            }
        }

        if (response && response.statusText) {
            return response.statusText;
        }

        return 'Eroare la comunicarea cu serverul.';
    }

    function validateSuccessPayload(payload) {
        if (!payload || typeof payload !== 'object') {
            throw new Error('Payload JSON lipsă sau invalid.');
        }

        if (payload.status !== 'success') {
            const message =
                typeof payload.message === 'string' && payload.message.trim() !== ''
                    ? payload.message
                    : 'Serverul a răspuns cu o eroare.';
            throw new Error(message);
        }

        if (!Object.prototype.hasOwnProperty.call(payload, 'data')) {
            throw new Error('Răspunsul serverului nu conține câmpul data.');
        }

        return payload.data;
    }

    async function requestJson(path, options = {}) {
        const {
            method = 'GET',
            queryParams = {},
            body = null,
            headers = {},
            timeout = DEFAULT_TIMEOUT_MS,
            credentials = 'same-origin',
        } = options;

        const url = buildApiUrl(path, queryParams);
        const timeoutControl = createTimeoutSignal(timeout);

        const requestOptions = {
            method,
            credentials,
            headers: {
                ...headers,
            },
            signal: timeoutControl.signal,
        };

        if (body !== null && body !== undefined) {
            requestOptions.headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
        }

        let response;

        try {
            response = await fetch(url, requestOptions);
        } catch (error) {
            timeoutControl.clear();

            if (error && error.name === 'AbortError') {
                throw new Error('Cererea către server a expirat.');
            }

            throw new Error('Nu s-a putut realiza conexiunea cu serverul.');
        }

        timeoutControl.clear();

        const payload = await parseJsonSafely(response);

        if (!response.ok) {
            throw new Error(extractErrorMessage(response, payload));
        }

        return validateSuccessPayload(payload);
    }

    async function getJson(path, queryParams = {}, options = {}) {
        return requestJson(path, {
            ...options,
            method: 'GET',
            queryParams,
        });
    }

    async function postJson(path, body = {}, options = {}) {
        return requestJson(path, {
            ...options,
            method: 'POST',
            body,
        });
    }

    async function getRaw(url, options = {}) {
        const {
            timeout = DEFAULT_TIMEOUT_MS,
            credentials = 'same-origin',
            headers = {},
        } = options;

        const timeoutControl = createTimeoutSignal(timeout);

        try {
            const response = await fetch(url, {
                method: 'GET',
                credentials,
                headers,
                signal: timeoutControl.signal,
            });

            timeoutControl.clear();

            if (!response.ok) {
                throw new Error(response.statusText || 'Eroare la descărcarea resursei.');
            }

            return response;
        } catch (error) {
            timeoutControl.clear();

            if (error && error.name === 'AbortError') {
                throw new Error('Cererea către server a expirat.');
            }

            throw new Error('Nu s-a putut realiza conexiunea cu serverul.');
        }
    }

    window.appApi = {
        buildApiUrl,
        requestJson,
        getJson,
        postJson,
        getRaw,
    };
})();