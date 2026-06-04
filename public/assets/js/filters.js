/**
 * Module: Filters Management
 * Se ocupă de popularea selecturilor din API și auto-submit la schimbări
 */

// 1. Definim funcțiile de populare pe care le caută map.js
window.appFilters = {
    // Curăță un select și lasă doar opțiunea implicită (ex: "Selectare an")
    resetSelect(element, defaultText) {
        if (!element) return;
        element.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = defaultText;
        element.appendChild(defaultOption);
    },

    // Adaugă opțiunile venite de la server în selecturi
    populateSelect(element, items, isPlainArray = false) {
        if (!element || !items || !Array.isArray(items)) return;

        items.forEach(item => {
            let value, label;

            if (isPlainArray) {
                // Pentru ani (vin ca un array simplu: [2020, 2021...])
                value = item;
                label = item;
            } else {
                // Pentru combustibil și categorii (vin ca obiecte: { name: "DIESEL" })
                value = item.name;
                label = item.name;
            }

            // Folosim utilitarul din utils.js dacă există, altfel creăm nativ
            if (window.appUtils && typeof window.appUtils.createOption === 'function') {
                element.appendChild(window.appUtils.createOption(value, label));
            } else {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                element.appendChild(opt);
            }
        });
    }
};

// 2. Logica ta excelentă de auto-submit (o rulăm după ce se încarcă DOM-ul)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('map-filters-form');
    if (!form) return;

    const year = document.getElementById('map-filter-year');
    const fuel = document.getElementById('map-filter-fuel-type');
    const nat = document.getElementById('map-filter-national-category');

    let debounceTimer = null;

    function submitForm() {
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    }

    function handleChange() {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            submitForm();
        }, 220);
    }

    // Punem ascultătorul de evenimente pe fiecare select disponibil
    [year, fuel, nat].forEach((el) => {
        if (!el) return;
        el.addEventListener('change', handleChange);
    });

    // Permitem tasta Enter pentru aplicare rapidă
    form.addEventListener('keydown', (ev) => {
        if (ev.key === 'Enter') {
            ev.preventDefault();
            submitForm();
        }
    });
});