    </div>
    
    <?php require_once __DIR__ . '/media_picker.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const bulkSaveButton = document.querySelector('[data-bulk-save-trigger]');
        const statusBox = document.querySelector('[data-bulk-save-status]');
        const toolbarWrapper = document.querySelector('[data-bulk-save-wrapper]');
        const bulkForms = Array.from(document.querySelectorAll('form[data-bulk-save="true"]'));

        if (!bulkSaveButton) {
            return;
        }

        document.body.classList.add('bulk-save-active');

        if (!bulkForms.length) {
            if (toolbarWrapper) {
                toolbarWrapper.classList.add('d-none');
            }
            return;
        }

        const restoreButton = () => {
            bulkSaveButton.disabled = false;
            const originalLabel = bulkSaveButton.getAttribute('data-original-label');
            if (originalLabel) {
                bulkSaveButton.innerHTML = originalLabel;
            }
        };

        bulkSaveButton.addEventListener('click', async function () {
            if (!bulkForms.length) {
                return;
            }

            const originalLabel = bulkSaveButton.getAttribute('data-original-label') || bulkSaveButton.innerHTML;
            bulkSaveButton.setAttribute('data-original-label', originalLabel);

            bulkSaveButton.disabled = true;
            bulkSaveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

            if (statusBox) {
                statusBox.className = 'alert alert-info mt-3';
                statusBox.textContent = 'Saving all visible sections. Please wait...';
                statusBox.classList.remove('d-none');
            }

            const failedSections = [];
            let successCount = 0;

            for (const form of bulkForms) {
                if (form.dataset.bulkSaveIncludeHidden !== 'true' && (form.offsetParent === null)) {
                    continue;
                }

                const sectionName = form.dataset.sectionName || 'Section';

                if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
                    failedSections.push(sectionName + ' (missing required fields)');
                    break;
                }

                const formData = new FormData(form);
                const actionAttr = form.getAttribute('action') || window.location.href;
                const method = (form.getAttribute('method') || 'POST').toUpperCase();

                try {
                    const response = await fetch(new URL(actionAttr, window.location.href), {
                        method,
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    successCount++;
                } catch (error) {
                    failedSections.push(sectionName);
                }
            }

            if (statusBox) {
                if (failedSections.length === 0) {
                    statusBox.className = 'alert alert-success mt-3';
                    statusBox.innerHTML = '<i class="fas fa-check-circle me-2"></i>All visible sections were saved successfully (' + successCount + ').';
                } else {
                    statusBox.className = 'alert alert-warning mt-3';
                    statusBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Some sections could not be saved: ' + failedSections.join(', ') + '.';
                }
            }

            restoreButton();
        });
    });
    </script>
</body>
</html>
