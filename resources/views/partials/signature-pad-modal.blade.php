@php
    $modalId = $modalId ?? 'signature-pad-modal';
    $canvasId = $canvasId ?? 'signature-pad-canvas';
    $clearButtonId = $clearButtonId ?? 'signature-pad-clear';
    $confirmButtonId = $confirmButtonId ?? 'signature-pad-confirm';
    $cancelSelector = $cancelSelector ?? 'data-signature-close';
    $title = $title ?? 'Signature Required';
    $message = $message ?? 'Please sign before continuing.';
    $confirmText = $confirmText ?? 'Confirm';
@endphp

<div id="{{ $modalId }}"
     class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6"
     aria-hidden="true">
    <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-300">E-Signature</p>
                <h3 class="mt-2 text-xl font-semibold text-white">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-400">{{ $message }}</p>
            </div>

            <button type="button"
                    {{ $cancelSelector }}
                    class="rounded-lg border border-slate-700 px-3 py-2 text-slate-400 transition hover:bg-slate-800 hover:text-white">
                &times;
            </button>
        </div>

        <div class="px-6 py-5">
            <div class="rounded-2xl border border-slate-700 bg-slate-900/70 p-3">
                <canvas id="{{ $canvasId }}"
                        class="h-56 w-full rounded-xl border border-dashed border-slate-600 bg-slate-950 touch-none"></canvas>
            </div>
            <p class="mt-3 text-xs text-slate-500">Use your mouse, touch, or pen to provide the Department Head signature.</p>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-slate-800 px-6 py-4">
            <button type="button"
                    id="{{ $clearButtonId }}"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-slate-800">
                Clear
            </button>

            <div class="flex items-center gap-3">
                <button type="button"
                        {{ $cancelSelector }}
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-slate-800">
                    Cancel
                </button>
                <button type="button"
                        id="{{ $confirmButtonId }}"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let signaturePadHasInk = false;
    let signaturePadPointerActive = false;
    let signaturePadContext = null;

    function getElements() {
        return {
            modal: document.getElementById('{{ $modalId }}'),
            canvas: document.getElementById('{{ $canvasId }}'),
            clearButton: document.getElementById('{{ $clearButtonId }}'),
            confirmButton: document.getElementById('{{ $confirmButtonId }}')
        };
    }

    function resizeCanvas() {
        const { canvas } = getElements();
        if (!canvas) return;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;

        const image = canvas.toDataURL('image/png');
        const hadInk = signaturePadHasInk;

        canvas.width = Math.floor(rect.width * ratio);
        canvas.height = Math.floor(rect.height * ratio);

        signaturePadContext = canvas.getContext('2d');
        if (!signaturePadContext) return;

        signaturePadContext.scale(ratio, ratio);
        signaturePadContext.lineCap = 'round';
        signaturePadContext.lineJoin = 'round';
        signaturePadContext.lineWidth = 2.2;
        signaturePadContext.strokeStyle = '#f8fafc';
        signaturePadContext.fillStyle = '#020617';
        signaturePadContext.fillRect(0, 0, rect.width, rect.height);

        if (hadInk) {
            const restored = new Image();
            restored.onload = () => {
                signaturePadContext.drawImage(restored, 0, 0, rect.width, rect.height);
            };
            restored.src = image;
        }
    }

    function clearPad() {
        const { canvas } = getElements();
        if (!canvas) return;
        signaturePadHasInk = false;
        signaturePadPointerActive = false;
        resizeCanvas();
    }

    function beginStroke(event) {
        const { canvas } = getElements();
        if (!canvas || !signaturePadContext) return;
        const rect = canvas.getBoundingClientRect();
        const pointX = (event.clientX ?? event.touches?.[0]?.clientX ?? 0) - rect.left;
        const pointY = (event.clientY ?? event.touches?.[0]?.clientY ?? 0) - rect.top;
        signaturePadPointerActive = true;
        signaturePadContext.beginPath();
        signaturePadContext.moveTo(pointX, pointY);
        if (event.cancelable) event.preventDefault();
    }

    function moveStroke(event) {
        const { canvas } = getElements();
        if (!canvas || !signaturePadContext || !signaturePadPointerActive) return;
        const rect = canvas.getBoundingClientRect();
        const pointX = (event.clientX ?? event.touches?.[0]?.clientX ?? 0) - rect.left;
        const pointY = (event.clientY ?? event.touches?.[0]?.clientY ?? 0) - rect.top;
        signaturePadContext.lineTo(pointX, pointY);
        signaturePadContext.stroke();
        signaturePadHasInk = true;
        if (event.cancelable) event.preventDefault();
    }

    function endStroke() {
        if (!signaturePadContext) return;
        signaturePadPointerActive = false;
        signaturePadContext.closePath();
    }

    window.addEventListener('resize', resizeCanvas);

    document.addEventListener('DOMContentLoaded', () => {
        const { modal, canvas, clearButton } = getElements();
        if (!modal || !canvas) return;

        canvas.addEventListener('mousedown', beginStroke);
        canvas.addEventListener('mousemove', moveStroke);
        canvas.addEventListener('mouseup', endStroke);
        canvas.addEventListener('mouseleave', endStroke);

        canvas.addEventListener('touchstart', beginStroke, { passive: false });
        canvas.addEventListener('touchmove', moveStroke, { passive: false });
        canvas.addEventListener('touchend', endStroke);

        clearButton?.addEventListener('click', clearPad);

        // Export global helpers if needed
        window.clearSignaturePad_{{ str_replace('-', '_', $modalId) }} = clearPad;
        window.getSignatureData_{{ str_replace('-', '_', $modalId) }} = () => {
            if (!signaturePadHasInk) return null;
            return canvas.toDataURL('image/png');
        };
        
        // Listen for modal show
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && !modal.classList.contains('hidden')) {
                    setTimeout(resizeCanvas, 50);
                }
            });
        });
        observer.observe(modal, { attributes: true });
    });
})();
</script>
