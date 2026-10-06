<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVG / Image → PNG Converter</title>

    <!-- JSZip for bulk download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- FileSaver for triggering downloads -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .card {
            background: #1e293b;
            border-radius: 16px;
            padding: 36px;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h1 i { color: #818cf8; }

        .subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 28px;
        }

        /* Drop zone */
        #drop-zone {
            border: 2px dashed #475569;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            background: #0f172a;
            position: relative;
        }

        #drop-zone.drag-over {
            border-color: #818cf8;
            background: #1e1b4b;
        }

        #drop-zone i {
            font-size: 3rem;
            color: #475569;
            display: block;
            margin-bottom: 12px;
            transition: color 0.25s;
        }

        #drop-zone.drag-over i { color: #818cf8; }

        #drop-zone p {
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 6px;
        }

        #drop-zone span {
            font-size: 0.8rem;
            color: #64748b;
        }

        #file-input { display: none; }

        .browse-btn {
            display: inline-block;
            margin-top: 14px;
            padding: 8px 22px;
            background: #4f46e5;
            color: #fff;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            border: none;
        }
        .browse-btn:hover { background: #6366f1; }

        /* Stats bar */
        #stats-bar {
            display: none;
            margin-top: 20px;
            padding: 14px 18px;
            background: #0f172a;
            border-radius: 10px;
            font-size: 0.88rem;
            color: #94a3b8;
            display: none;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        #stats-bar.visible { display: flex; }

        .stat { display: flex; align-items: center; gap: 6px; }
        .stat i { color: #818cf8; }

        /* Action buttons */
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 9px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s;
        }

        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover:not(:disabled) { background: #6366f1; }
        .btn-success { background: #059669; color: #fff; }
        .btn-success:hover:not(:disabled) { background: #10b981; }
        .btn-danger  { background: #dc2626; color: #fff; }
        .btn-danger:hover:not(:disabled)  { background: #ef4444; }
        .btn:disabled { opacity: 0.45; cursor: not-allowed; }

        /* Progress */
        #progress-wrap {
            display: none;
            margin-top: 18px;
        }

        #progress-wrap.visible { display: block; }

        .progress-bar-bg {
            background: #0f172a;
            border-radius: 99px;
            height: 8px;
            overflow: hidden;
            margin-top: 6px;
        }

        #progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            border-radius: 99px;
            width: 0%;
            transition: width 0.3s;
        }

        #progress-label {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        /* Grid */
        #preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            margin-top: 28px;
        }

        .preview-item {
            background: #0f172a;
            border-radius: 12px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            border: 1px solid #1e293b;
            position: relative;
            transition: border-color 0.2s;
        }

        .preview-item.done { border-color: #059669; }
        .preview-item.error { border-color: #dc2626; }

        .preview-item canvas {
            border-radius: 6px;
            background: repeating-conic-gradient(#334155 0% 25%, #1e293b 0% 50%) 0 0 / 12px 12px;
            display: block;
        }

        .preview-name {
            font-size: 0.72rem;
            color: #94a3b8;
            text-align: center;
            word-break: break-all;
            line-height: 1.3;
            max-width: 100%;
        }

        .preview-status {
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-pending  { color: #f59e0b; }
        .status-done     { color: #10b981; }
        .status-error    { color: #f87171; }

        .item-dl-btn {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #1e293b;
            color: #818cf8;
            border: 1px solid #334155;
            cursor: pointer;
            display: none;
            transition: background 0.2s;
        }

        .item-dl-btn:hover { background: #334155; }

        .remove-item {
            position: absolute;
            top: 6px;
            right: 6px;
            background: #dc2626;
            border: none;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.65rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .remove-item:hover { background: #ef4444; }

        /* Empty state */
        #empty-state {
            text-align: center;
            padding: 30px 0 10px;
            color: #475569;
            font-size: 0.88rem;
            display: none;
        }

        #empty-state.visible { display: block; }

        /* Toast */
        #toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            background: #1e293b;
            color: #e2e8f0;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.88rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            border-left: 4px solid #4f46e5;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.3s;
            z-index: 1000;
            max-width: 320px;
        }

        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        #toast.toast-success { border-color: #10b981; }
        #toast.toast-error   { border-color: #f87171; }
    </style>
</head>
<body>

<div class="card">
    <h1><i class="fa-solid fa-wand-magic-sparkles"></i> Image → 140×140 PNG Converter</h1>
    <p class="subtitle">Upload SVG, PNG, JPEG, WebP, GIF, BMP or any image — each resized to 140×140 PNG. All in-browser, no upload.</p>

    <!-- Drop Zone -->
    <div id="drop-zone">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <p>Drag &amp; drop images here</p>
        <span>SVG · PNG · JPG · JPEG · WebP · GIF · BMP · and more</span><br>
        <button class="browse-btn" onclick="document.getElementById('file-input').click()">
            <i class="fa-solid fa-folder-open"></i> Browse Files
        </button>
        <input type="file" id="file-input" accept="image/*" multiple>
    </div>

    <!-- Stats Bar -->
    <div id="stats-bar">
        <div class="stat"><i class="fa-solid fa-file-image"></i> <span id="stat-total">0</span> files</div>
        <div class="stat"><i class="fa-solid fa-check-circle" style="color:#10b981"></i> <span id="stat-done">0</span> done</div>
        <div class="stat"><i class="fa-solid fa-clock" style="color:#f59e0b"></i> <span id="stat-pending">0</span> pending</div>
        <div class="stat"><i class="fa-solid fa-circle-xmark" style="color:#f87171"></i> <span id="stat-error">0</span> errors</div>
    </div>

    <!-- Action Buttons -->
    <div class="actions">
        <button class="btn btn-primary" id="btn-convert" disabled>
            <i class="fa-solid fa-play"></i> Convert All
        </button>
        <button class="btn btn-success" id="btn-download-all" disabled>
            <i class="fa-solid fa-file-zipper"></i> Download All (ZIP)
        </button>
        <button class="btn btn-danger" id="btn-clear" disabled>
            <i class="fa-solid fa-trash"></i> Clear All
        </button>
    </div>

    <!-- Progress -->
    <div id="progress-wrap">
        <div id="progress-label">Converting 0 / 0...</div>
        <div class="progress-bar-bg">
            <div id="progress-fill"></div>
        </div>
    </div>

    <!-- Preview Grid -->
    <div id="preview-grid"></div>
    <div id="empty-state" class="visible">
        <i class="fa-solid fa-images" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        No image files added yet
    </div>
</div>

<!-- Toast -->
<div id="toast"></div>

<script>
// ─────────────────────────────────────────────
//  State
// ─────────────────────────────────────────────
const OUTPUT_SIZE = 140;
let files = [];     // { id, name, type, svgText|dataUrl, canvas, blob, status }
let nextId = 0;

// ─────────────────────────────────────────────
//  DOM refs
// ─────────────────────────────────────────────
const dropZone      = document.getElementById('drop-zone');
const fileInput     = document.getElementById('file-input');
const statsBar      = document.getElementById('stats-bar');
const statTotal     = document.getElementById('stat-total');
const statDone      = document.getElementById('stat-done');
const statPending   = document.getElementById('stat-pending');
const statError     = document.getElementById('stat-error');
const btnConvert    = document.getElementById('btn-convert');
const btnDownloadAll= document.getElementById('btn-download-all');
const btnClear      = document.getElementById('btn-clear');
const progressWrap  = document.getElementById('progress-wrap');
const progressFill  = document.getElementById('progress-fill');
const progressLabel = document.getElementById('progress-label');
const previewGrid   = document.getElementById('preview-grid');
const emptyState    = document.getElementById('empty-state');

// ─────────────────────────────────────────────
//  Drag & drop
// ─────────────────────────────────────────────
['dragenter','dragover'].forEach(e => {
    dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('drag-over'); });
});

['dragleave','drop'].forEach(e => {
    dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('drag-over'); });
});

dropZone.addEventListener('drop', ev => {
    const dropped = Array.from(ev.dataTransfer.files).filter(f => f.type.startsWith('image/') || f.name.match(/\.(svg|png|jpe?g|webp|gif|bmp|ico|tiff?)$/i));
    if (dropped.length) addFiles(dropped);
    else showToast('No valid image files found in the drop.', 'error');
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length) addFiles(Array.from(fileInput.files));
    fileInput.value = '';
});

// ─────────────────────────────────────────────
//  Detect if file is SVG
// ─────────────────────────────────────────────
function isSvgFile(f) {
    return f.type === 'image/svg+xml' || f.name.toLowerCase().endsWith('.svg');
}

// ─────────────────────────────────────────────
//  Add files — handles both SVG and raster images
// ─────────────────────────────────────────────
function addFiles(fileList) {
    const readers = fileList.map(f => new Promise(resolve => {
        const reader = new FileReader();
        if (isSvgFile(f)) {
            reader.onload = e => resolve({
                id: nextId++, name: f.name, type: 'svg',
                svgText: e.target.result, dataUrl: null,
                canvas: null, blob: null, status: 'pending'
            });
            reader.readAsText(f);
        } else {
            reader.onload = e => resolve({
                id: nextId++, name: f.name, type: 'raster',
                svgText: null, dataUrl: e.target.result,
                canvas: null, blob: null, status: 'pending'
            });
            reader.readAsDataURL(f);
        }
    }));

    Promise.all(readers).then(newItems => {
        files.push(...newItems);
        newItems.forEach(addPreviewCard);
        updateUI();
    });
}

// ─────────────────────────────────────────────
//  Preview card
// ─────────────────────────────────────────────
function addPreviewCard(item) {
    const card = document.createElement('div');
    card.className = 'preview-item';
    card.id = 'card-' + item.id;

    const canvas = document.createElement('canvas');
    canvas.width  = OUTPUT_SIZE;
    canvas.height = OUTPUT_SIZE;
    canvas.style.width  = OUTPUT_SIZE + 'px';
    canvas.style.height = OUTPUT_SIZE + 'px';
    item.canvas = canvas;

    // Show preview inline at 140×140
    if (item.type === 'svg') {
        renderSvgOnCanvas(item.svgText, canvas).catch(() => {});
    } else {
        renderRasterOnCanvas(item.dataUrl, canvas).catch(() => {});
    }

    // Badge to show file type
    const badge = document.createElement('span');
    badge.style.cssText = 'position:absolute;top:6px;left:6px;background:#334155;color:#94a3b8;font-size:0.6rem;font-weight:700;padding:2px 6px;border-radius:4px;text-transform:uppercase;';
    badge.textContent = item.type === 'svg' ? 'SVG' : item.name.split('.').pop().toUpperCase();
    card.appendChild(badge);

    const nameEl = document.createElement('div');
    nameEl.className = 'preview-name';
    nameEl.textContent = item.name.replace(/\.[^.]+$/, '');

    const statusEl = document.createElement('div');
    statusEl.className = 'preview-status status-pending';
    statusEl.id = 'status-' + item.id;
    statusEl.textContent = 'Pending';

    const dlBtn = document.createElement('button');
    dlBtn.className = 'item-dl-btn';
    dlBtn.id = 'dl-' + item.id;
    dlBtn.innerHTML = '<i class="fa-solid fa-download"></i> PNG';
    dlBtn.addEventListener('click', () => downloadSingle(item));

    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-item';
    removeBtn.title = 'Remove';
    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    removeBtn.addEventListener('click', () => removeFile(item.id));

    card.appendChild(removeBtn);
    card.appendChild(canvas);
    card.appendChild(nameEl);
    card.appendChild(statusEl);
    card.appendChild(dlBtn);

    previewGrid.appendChild(card);
}

// ─────────────────────────────────────────────
//  SVG → Canvas (returns Promise)
// ─────────────────────────────────────────────
function renderSvgOnCanvas(svgText, canvas) {
    return new Promise((resolve, reject) => {
        // Fix missing namespace
        let svg = svgText.trim();
        if (!svg.includes('xmlns')) {
            svg = svg.replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"');
        }

        // Parse to get natural viewBox / width / height
        const parser = new DOMParser();
        const doc    = parser.parseFromString(svg, 'image/svg+xml');
        const svgEl  = doc.querySelector('svg');

        if (!svgEl || doc.querySelector('parsererror')) {
            reject(new Error('Invalid SVG'));
            return;
        }

        // Ensure the SVG has explicit width/height set to OUTPUT_SIZE for correct rendering
        svgEl.setAttribute('width',  OUTPUT_SIZE);
        svgEl.setAttribute('height', OUTPUT_SIZE);

        // If no viewBox, try to set one from existing w/h so content scales correctly
        if (!svgEl.getAttribute('viewBox')) {
            const w = parseFloat(svgEl.getAttribute('width'))  || OUTPUT_SIZE;
            const h = parseFloat(svgEl.getAttribute('height')) || OUTPUT_SIZE;
            svgEl.setAttribute('viewBox', `0 0 ${w} ${h}`);
            svgEl.setAttribute('width',  OUTPUT_SIZE);
            svgEl.setAttribute('height', OUTPUT_SIZE);
        }

        const serializer = new XMLSerializer();
        const svgStr     = serializer.serializeToString(svgEl);
        const blob       = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
        const url        = URL.createObjectURL(blob);
        const img        = new Image();

        img.onload = () => {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, OUTPUT_SIZE, OUTPUT_SIZE);
            ctx.drawImage(img, 0, 0, OUTPUT_SIZE, OUTPUT_SIZE);
            URL.revokeObjectURL(url);
            resolve();
        };

        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('Image load failed'));
        };

        img.src = url;
    });
}

// ─────────────────────────────────────────────
//  Raster (PNG/JPEG/WebP/etc.) → Canvas 140×140
// ─────────────────────────────────────────────
function renderRasterOnCanvas(dataUrl, canvas) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, OUTPUT_SIZE, OUTPUT_SIZE);
            // Draw with object-fit: contain logic — center, preserve aspect ratio
            const scale = Math.min(OUTPUT_SIZE / img.naturalWidth, OUTPUT_SIZE / img.naturalHeight);
            const w = img.naturalWidth  * scale;
            const h = img.naturalHeight * scale;
            const x = (OUTPUT_SIZE - w) / 2;
            const y = (OUTPUT_SIZE - h) / 2;
            ctx.drawImage(img, x, y, w, h);
            resolve();
        };
        img.onerror = () => reject(new Error('Image load failed'));
        img.src = dataUrl;
    });
}

// ─────────────────────────────────────────────
//  Convert all
// ─────────────────────────────────────────────
btnConvert.addEventListener('click', async () => {
    const pending = files.filter(f => f.status === 'pending' || f.status === 'error');
    if (!pending.length) { showToast('No pending files to convert.', 'error'); return; }

    btnConvert.disabled    = true;
    btnDownloadAll.disabled = true;
    btnClear.disabled      = true;

    progressWrap.classList.add('visible');

    let done = 0;
    const total = pending.length;

    for (const item of pending) {
        progressLabel.textContent = `Converting ${done + 1} / ${total}...`;
        progressFill.style.width  = Math.round((done / total) * 100) + '%';

        try {
            if (item.type === 'svg') {
                await renderSvgOnCanvas(item.svgText, item.canvas);
            } else {
                await renderRasterOnCanvas(item.dataUrl, item.canvas);
            }

            // Canvas → Blob
            item.blob = await canvasToBlob(item.canvas);
            item.status = 'done';

            const card = document.getElementById('card-' + item.id);
            const statusEl = document.getElementById('status-' + item.id);
            const dlBtn    = document.getElementById('dl-' + item.id);

            if (card)     card.classList.add('done');
            if (statusEl) { statusEl.textContent = 'Done ✓'; statusEl.className = 'preview-status status-done'; }
            if (dlBtn)    dlBtn.style.display = 'inline-block';

        } catch (err) {
            item.status = 'error';
            const card     = document.getElementById('card-' + item.id);
            const statusEl = document.getElementById('status-' + item.id);
            if (card)     card.classList.add('error');
            if (statusEl) { statusEl.textContent = 'Error ✗'; statusEl.className = 'preview-status status-error'; }
        }

        done++;
        updateStats();
    }

    progressFill.style.width = '100%';
    progressLabel.textContent = `Done! ${done} file(s) processed.`;

    setTimeout(() => progressWrap.classList.remove('visible'), 2000);

    const allDone = files.filter(f => f.status === 'done');
    btnDownloadAll.disabled = allDone.length === 0;
    btnConvert.disabled     = files.filter(f => f.status === 'pending').length === 0;
    btnClear.disabled       = files.length === 0;

    showToast(`Converted ${allDone.length} file(s) successfully.`, 'success');
});

// ─────────────────────────────────────────────
//  Canvas → Blob (Promise)
// ─────────────────────────────────────────────
function canvasToBlob(canvas) {
    return new Promise(resolve => canvas.toBlob(blob => resolve(blob), 'image/png'));
}

// ─────────────────────────────────────────────
//  Download single
// ─────────────────────────────────────────────
function downloadSingle(item) {
    if (!item.blob) return;
    const pngName = item.name.replace(/\.[^.]+$/, '') + '.png';
    saveAs(item.blob, pngName);
}

// ─────────────────────────────────────────────
//  Download all as ZIP
// ─────────────────────────────────────────────
btnDownloadAll.addEventListener('click', async () => {
    const done = files.filter(f => f.status === 'done' && f.blob);
    if (!done.length) { showToast('Nothing to download yet.', 'error'); return; }

    btnDownloadAll.disabled = true;
    showToast('Building ZIP…', 'success');

    const zip = new JSZip();
    done.forEach(item => {
        const pngName = item.name.replace(/\.[^.]+$/, '') + '.png';
        zip.file(pngName, item.blob);
    });

    const zipBlob = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE' });
    saveAs(zipBlob, 'converted-pngs.zip');
    btnDownloadAll.disabled = false;
    showToast(`Downloaded ${done.length} PNG(s) in ZIP.`, 'success');
});

// ─────────────────────────────────────────────
//  Remove file
// ─────────────────────────────────────────────
function removeFile(id) {
    files = files.filter(f => f.id !== id);
    const card = document.getElementById('card-' + id);
    if (card) card.remove();
    updateUI();
}

// ─────────────────────────────────────────────
//  Clear all
// ─────────────────────────────────────────────
btnClear.addEventListener('click', () => {
    files = [];
    previewGrid.innerHTML = '';
    updateUI();
    showToast('Cleared all files.', 'success');
});

// ─────────────────────────────────────────────
//  UI helpers
// ─────────────────────────────────────────────
function updateUI() {
    const hasFiles = files.length > 0;

    emptyState.classList.toggle('visible', !hasFiles);
    statsBar.classList.toggle('visible', hasFiles);

    btnConvert.disabled     = !files.some(f => f.status === 'pending' || f.status === 'error');
    btnDownloadAll.disabled = !files.some(f => f.status === 'done');
    btnClear.disabled       = !hasFiles;

    updateStats();
}

function updateStats() {
    statTotal.textContent   = files.length;
    statDone.textContent    = files.filter(f => f.status === 'done').length;
    statPending.textContent = files.filter(f => f.status === 'pending').length;
    statError.textContent   = files.filter(f => f.status === 'error').length;
}

// ─────────────────────────────────────────────
//  Toast
// ─────────────────────────────────────────────
let toastTimer;
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'show toast-' + type;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
}
</script>
</body>
</html>
