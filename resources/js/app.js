const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('#primary-navigation');

if (menuToggle instanceof HTMLButtonElement && navigation instanceof HTMLElement) {
    menuToggle.addEventListener('click', () => {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isExpanded));
        navigation.classList.toggle('is-open', !isExpanded);
    });

    navigation.addEventListener('click', (event) => {
        if (event.target instanceof HTMLAnchorElement && window.matchMedia('(max-width: 760px)').matches) {
            menuToggle.setAttribute('aria-expanded', 'false');
            navigation.classList.remove('is-open');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            menuToggle.setAttribute('aria-expanded', 'false');
            navigation.classList.remove('is-open');
        }
    });
}

document.querySelectorAll('[data-image-preview]').forEach((imageInput) => {
    if (!(imageInput instanceof HTMLInputElement)) {
        return;
    }

    const imagePreview = document.querySelector(`[data-preview-image="${imageInput.dataset.imagePreview}"]`);

    if (!(imagePreview instanceof HTMLImageElement)) {
        return;
    }

    imageInput.addEventListener('change', () => {
        const [file] = imageInput.files ?? [];

        if (file) {
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.hidden = false;
        }
    });
});

const oraTrigger = document.querySelector('[data-ora-trigger]');
const oraPanel = document.querySelector('#ora-panel');
const oraClose = document.querySelector('[data-ora-close]');
const oraForm = document.querySelector('[data-ora-form]');
const oraInput = document.querySelector('[data-ora-input]');
const oraMessages = document.querySelector('[data-ora-messages]');

const toggleOra = (isOpen) => {
    if (!(oraTrigger instanceof HTMLButtonElement) || !(oraPanel instanceof HTMLElement)) {
        return;
    }

    oraPanel.hidden = !isOpen;
    oraTrigger.setAttribute('aria-expanded', String(isOpen));

    if (isOpen && oraInput instanceof HTMLInputElement) {
        oraInput.focus();
    }
};

const addOraMessage = (message, type, link = null) => {
    if (!(oraMessages instanceof HTMLElement)) {
        return;
    }

    const messageElement = document.createElement('p');
    messageElement.className = `ora-message ora-message-${type}`;
    messageElement.textContent = message;
    oraMessages.append(messageElement);

    if (link && typeof link.url === 'string' && typeof link.label === 'string') {
        const linkElement = document.createElement('a');
        linkElement.className = 'ora-message-link';
        linkElement.href = link.url;
        linkElement.textContent = link.label;
        oraMessages.append(linkElement);
    }

    oraMessages.scrollTop = oraMessages.scrollHeight;
};

if (oraTrigger instanceof HTMLButtonElement && oraPanel instanceof HTMLElement) {
    oraTrigger.addEventListener('click', () => toggleOra(oraPanel.hidden));
    oraClose?.addEventListener('click', () => toggleOra(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !oraPanel.hidden) {
            toggleOra(false);
            oraTrigger.focus();
        }
    });
}

if (oraForm instanceof HTMLFormElement && oraInput instanceof HTMLInputElement) {
    oraForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = oraInput.value.trim();
        const submitButton = oraForm.querySelector('button[type="submit"]');

        if (!message || !(submitButton instanceof HTMLButtonElement)) {
            return;
        }

        addOraMessage(message, 'user');
        oraInput.value = '';
        oraInput.disabled = true;
        submitButton.disabled = true;

        try {
            const response = await fetch('/ora/messages', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                },
                body: JSON.stringify({ message }),
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error('ORA could not answer right now. Please try again.');
            }

            addOraMessage(data.reply, 'assistant', data.link);
        } catch (error) {
            addOraMessage(error instanceof Error ? error.message : 'ORA could not answer right now. Please try again.', 'assistant');
        } finally {
            oraInput.disabled = false;
            submitButton.disabled = false;
            oraInput.focus();
        }
    });
}
