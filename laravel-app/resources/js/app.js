import './bootstrap';

const form = document.querySelector('[data-upload-form]');

if (form) {
    const input = document.querySelector('#image-input');
    const dropZone = document.querySelector('#drop-zone');
    const browseButton = document.querySelector('#browse-button');
    const errorBox = document.querySelector('#upload-error');
    const submitButton = document.querySelector('#submit-button');
    const submitLabel = document.querySelector('#submit-label');
    const spinner = document.querySelector('#submit-spinner');
    const arrow = document.querySelector('#submit-arrow');
    const resultSection = document.querySelector('#analysis-result');
    const maxBytes = Number(form.dataset.maxBytes);
    const allowedExtensions = new Set(['jpg', 'jpeg', 'png', 'webp']);
    let previewUrl = null;

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }

    function formatSize(bytes) {
        return bytes < 1024 * 1024
            ? `${(bytes / 1024).toFixed(1)} KB`
            : `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
    }

    function clearPreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }
        document.querySelector('#image-preview').removeAttribute('src');
        document.querySelector('#preview-filename').textContent = '';
        document.querySelector('#preview-size').textContent = '';
        document.querySelector('#preview-state').classList.add('hidden');
        document.querySelector('#empty-state').classList.remove('hidden');
    }

    function updateSelection(file) {
        clearError();
        resultSection.classList.add('hidden');
        if (!file) {
            clearPreview();
            return;
        }

        document.querySelector('#empty-state').classList.add('hidden');
        document.querySelector('#preview-state').classList.remove('hidden');
        document.querySelector('#preview-filename').textContent = file.name;
        document.querySelector('#preview-size').textContent = formatSize(file.size);

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
        previewUrl = URL.createObjectURL(file);
        document.querySelector('#image-preview').src = previewUrl;

        const extension = file.name.split('.').pop()?.toLowerCase();
        if (!extension || !allowedExtensions.has(extension)) {
            showError('Choose a JPG, JPEG, PNG, or WEBP image.');
        } else if (file.size > maxBytes) {
            showError(`The image must not be larger than ${form.dataset.maxSizeMb} MB.`);
        }
    }

    function setLoading(loading) {
        submitButton.disabled = loading;
        spinner.classList.toggle('hidden', !loading);
        arrow.classList.toggle('hidden', loading);
        submitLabel.textContent = loading ? 'Analyzing image…' : 'Upload and Analyze';
    }

    function setResult(result) {
        document.querySelector('#result-message').textContent = result.message;
        document.querySelector('#result-filename').textContent = result.filename;
        document.querySelector('#result-format').textContent = result.format;
        document.querySelector('#result-dimensions').textContent = `${result.width} × ${result.height} px`;
        document.querySelector('#result-file_size_bytes').textContent = formatSize(result.file_size_bytes);
        resultSection.classList.remove('hidden');
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    browseButton.addEventListener('click', () => input.click());
    input.addEventListener('change', () => updateSelection(input.files[0]));

    for (const eventName of ['dragenter', 'dragover']) {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
        });
    }
    for (const eventName of ['dragleave', 'drop']) {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
        });
    }
    dropZone.addEventListener('drop', (event) => {
        const [file] = event.dataTransfer.files;
        if (!file) {
            return;
        }
        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        updateSelection(file);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearError();

        const file = input.files[0];
        if (!file) {
            showError('Choose an image to analyze.');
            return;
        }
        const extension = file.name.split('.').pop()?.toLowerCase();
        if (!extension || !allowedExtensions.has(extension)) {
            showError('Choose a JPG, JPEG, PNG, or WEBP image.');
            return;
        }
        if (file.size > maxBytes) {
            showError(`The image must not be larger than ${form.dataset.maxSizeMb} MB.`);
            return;
        }

        setLoading(true);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            let payload;
            try {
                payload = await response.json();
            } catch {
                throw new Error('The server returned an unexpected response. Please try again.');
            }

            if (!response.ok) {
                const validationMessage = Object.values(payload.errors ?? {}).flat()[0];
                throw new Error(validationMessage ?? payload.message ?? 'The image could not be analyzed. Please try again.');
            }
            setResult(payload);
        } catch (error) {
            showError(error instanceof Error ? error.message : 'Image analysis is temporarily unavailable. Please try again.');
        } finally {
            setLoading(false);
        }
    });

    document.querySelector('#upload-another').addEventListener('click', () => {
        form.reset();
        clearError();
        clearPreview();
        resultSection.classList.add('hidden');
        document.querySelector('#upload').scrollIntoView({ behavior: 'smooth', block: 'center' });
        input.focus();
    });
}
