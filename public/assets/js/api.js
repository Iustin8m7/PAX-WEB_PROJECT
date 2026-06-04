window.appApi = {
    async getJson(url) {
        const response = await fetch(url, { credentials: 'same-origin' });
        const payload = await response.json();

        if (!response.ok || payload?.status !== 'success') {
            const message = payload?.message || response.statusText || 'Eroare la comunicarea cu serverul.';
            throw new Error(message);
        }

        return payload.data;
    },
};
