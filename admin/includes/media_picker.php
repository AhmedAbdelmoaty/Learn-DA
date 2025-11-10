<!-- Media Picker Modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaPickerModalLabel">
                    <i class="fas fa-images"></i> Choose from Media Library
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mediaPickerLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading media library...</p>
                </div>
                <div id="mediaPickerContent" style="display: none;">
                    <div id="mediaPickerGrid" class="row g-3"></div>
                    <div id="mediaPickerEmpty" class="text-center py-5" style="display: none;">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No images in your media library yet.</p>
                        <a href="media_upload.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload"></i> Upload Images
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Picker Styles -->
<style>
    .media-picker-item {
        cursor: pointer;
        border: 3px solid transparent;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .media-picker-item:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(168, 50, 78, 0.3);
    }
    
    .media-picker-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    
    .media-picker-item .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        color: white;
        padding: 8px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .media-picker-item:hover .overlay {
        opacity: 1;
    }
    
    .image-preview-box {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        display: none;
    }
    
    .image-preview-box.active {
        display: block;
    }
    
    .image-preview-box img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 4px;
        border: 2px solid #dee2e6;
    }
    
    .btn-choose-media {
        white-space: nowrap;
    }
</style>

<!-- Media Picker Script -->
<script>
let currentMediaInputTarget = null;
let currentMediaPreviewTarget = null;

function openMediaPicker(inputId, previewId) {
    currentMediaInputTarget = document.getElementById(inputId);
    currentMediaPreviewTarget = previewId ? document.getElementById(previewId) : null;
    
    const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
    modal.show();
    
    loadMediaLibrary();
}

function loadMediaLibrary() {
    const loading = document.getElementById('mediaPickerLoading');
    const content = document.getElementById('mediaPickerContent');
    const grid = document.getElementById('mediaPickerGrid');
    const empty = document.getElementById('mediaPickerEmpty');
    
    loading.style.display = 'block';
    content.style.display = 'none';
    
    fetch('get_media.php')
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            content.style.display = 'block';
            
            if (data.success && data.images.length > 0) {
                grid.innerHTML = '';
                empty.style.display = 'none';
                
                data.images.forEach(image => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6';
                    
                    col.innerHTML = `
                        <div class="media-picker-item" onclick="selectMedia('${image.filepath}', '${image.filename}')">
                            <img src="../${image.filepath}" alt="${image.filename}">
                            <div class="overlay">
                                <div class="text-truncate">${image.filename}</div>
                            </div>
                        </div>
                    `;
                    
                    grid.appendChild(col);
                });
            } else {
                grid.innerHTML = '';
                empty.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading media:', error);
            loading.style.display = 'none';
            content.style.display = 'block';
            grid.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading media library</div></div>';
        });
}
function toAbsolute(path){
  const origin = window.location.origin; // مثال: https://your-app.replit.dev
  if (!path) return path;
  if (/^https?:\/\//i.test(path)) return path;   // لو أصلاً مطلق
  // شيل أي ../ في البداية ثم ابنِ الرابط المطلق
  const cleaned = path.replace(/^(\.\.\/)+/, '');
  if (path.startsWith('/')) return origin + path; // /assets/...
  return origin + '/' + cleaned;                  // assets/...
}

function selectMedia(filepath, filename) {
    if (currentMediaInputTarget) {
        const absolute = toAbsolute(filepath);
        currentMediaInputTarget.value = absolute;
                
        if (currentMediaPreviewTarget) {
            currentMediaPreviewTarget.classList.add('active');
            const img = currentMediaPreviewTarget.querySelector('img');
            if (img) {
                img.src = absolute;
                img.alt = filename;
            }
        }
        
        const event = new Event('change', { bubbles: true });
        currentMediaInputTarget.dispatchEvent(event);
    }
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal'));
    if (modal) {
        modal.hide();
    }
}
</script>
