<?php
if (!function_exists('renderBulkSaveToolbar')) {
    function renderBulkSaveToolbar(array $config = []): void
    {
        $pageTitle = $GLOBALS['page_title'] ?? 'Page Management';
        $defaults = [
            'enabled' => true,
            'button_label' => 'Save All Changes',
            'button_icon' => 'fas fa-save',
            'wrapper_class' => '',
            'aria_label' => $pageTitle,
        ];

        $config = array_merge($defaults, $config);

        if (!$config['enabled']) {
            return;
        }
        ?>
        <div class="bulk-save-toolbar-wrapper <?php echo htmlspecialchars($config['wrapper_class']); ?>" data-bulk-save-wrapper>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <button type="button"
                        class="btn btn-primary"
                        data-bulk-save-trigger
                        aria-label="<?php echo htmlspecialchars($config['aria_label']); ?>">
                    <i class="<?php echo htmlspecialchars($config['button_icon']); ?> me-2"></i>
                    <?php echo htmlspecialchars($config['button_label']); ?>
                </button>
            </div>
            <div class="alert d-none mt-3" role="alert" aria-live="polite" data-bulk-save-status></div>
        </div>
        <?php
    }
}
