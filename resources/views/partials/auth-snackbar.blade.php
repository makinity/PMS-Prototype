@php
    $pmsSnackbarInitial = null;

    if (session('error')) {
        $pmsSnackbarInitial = ['type' => 'error', 'message' => (string) session('error')];
    } elseif ($errors->any()) {
        $pmsSnackbarInitial = ['type' => 'error', 'message' => (string) $errors->first()];
    } elseif (session('success')) {
        $pmsSnackbarInitial = ['type' => 'success', 'message' => (string) session('success')];
    } elseif (session('info')) {
        $pmsSnackbarInitial = ['type' => 'info', 'message' => (string) session('info')];
    } elseif (session('warning')) {
        $pmsSnackbarInitial = ['type' => 'warning', 'message' => (string) session('warning')];
    }
@endphp

<div id="pms-snackbar-host" class="pointer-events-none fixed inset-x-0 top-6 z-[200] flex justify-center px-4 sm:px-6"></div>

<script>
    (() => {
        if (window.PMSnackbar) {
            return;
        }

        const host = document.getElementById('pms-snackbar-host');
        let activeToast = null;
        let hideTimer = null;
        let removeTimer = null;
        let lastActionMessage = '';
        let lastActionAt = 0;
        const ACTION_HINT_WINDOW_MS = 1600;

        const TYPE_META = {
            success: {
                tone: 'border-emerald-500/40 bg-emerald-500/15 text-emerald-100 shadow-emerald-950/30',
                icon: `<svg aria-hidden="true" class="h-5 w-5 shrink-0 text-emerald-300" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.78-9.72a.75.75 0 00-1.06-1.06L9.25 10.69 7.78 9.22a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l4-4z" clip-rule="evenodd" /></svg>`,
                role: 'status',
                live: 'polite',
                label: 'Success',
            },
            info: {
                tone: 'border-sky-500/40 bg-sky-500/15 text-sky-100 shadow-slate-950/30',
                icon: `<svg aria-hidden="true" class="h-5 w-5 shrink-0 text-sky-300" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-4a1 1 0 112 0 1 1 0 01-2 0zm2 8a1 1 0 10-2 0 1 1 0 002 0zm-2-5a1 1 0 000 2v2a1 1 0 102 0v-3a1 1 0 00-1-1h-1z" clip-rule="evenodd" /></svg>`,
                role: 'status',
                live: 'polite',
                label: 'Info',
            },
            warning: {
                tone: 'border-amber-500/40 bg-amber-500/15 text-amber-100 shadow-slate-950/30',
                icon: `<svg aria-hidden="true" class="h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.42c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.921zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-.993.883L9 8v3a1 1 0 001.993.117L11 11V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>`,
                role: 'status',
                live: 'polite',
                label: 'Warning',
            },
            error: {
                tone: 'border-rose-500/40 bg-rose-500/15 text-rose-100 shadow-rose-950/30',
                icon: `<svg aria-hidden="true" class="h-5 w-5 shrink-0 text-rose-300" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-5.28-2.22a.75.75 0 10-1.06-1.06L10 8.44 8.34 6.72a.75.75 0 10-1.08 1.04L8.94 9.5l-1.68 1.72a.75.75 0 101.08 1.04L10 10.56l1.66 1.7a.75.75 0 001.08-1.04L11.06 9.5l1.66-1.72z" clip-rule="evenodd" /></svg>`,
                role: 'alert',
                live: 'assertive',
                label: 'Error',
            },
        };

        const ACTION_MATCHERS = [
            { pattern: /\bapprove|approval|approving\b/i, message: 'Approving...' },
            { pattern: /\bsubmit|submitting\b/i, message: 'Submitting...' },
            { pattern: /\bendorse|endorsing\b/i, message: 'Endorsing...' },
            { pattern: /\breturn|returning\b/i, message: 'Returning...' },
            { pattern: /\bcommit|committing\b/i, message: 'Committing...' },
        ];

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function normalizeActionCandidate(value) {
            return String(value ?? '')
                .replace(/\s+/g, ' ')
                .replace(/\u2026/g, '...')
                .trim();
        }

        function resolveActionMessageFromText(value) {
            const normalized = normalizeActionCandidate(value);
            if (normalized === '') {
                return null;
            }

            for (const matcher of ACTION_MATCHERS) {
                if (matcher.pattern.test(normalized)) {
                    return matcher.message;
                }
            }

            return null;
        }

        function resolveActionMessage(trigger, form = null) {
            const explicitMessage = normalizeActionCandidate(trigger?.dataset?.snackbarMessage || form?.dataset?.snackbarMessage || '');
            if (explicitMessage !== '') {
                return explicitMessage;
            }

            const candidates = [
                trigger?.dataset?.loadingText,
                trigger?.dataset?.actionLoading,
                trigger?.getAttribute?.('aria-label'),
                trigger?.getAttribute?.('title'),
                trigger?.value,
                trigger?.textContent,
                form?.getAttribute?.('action'),
            ];

            for (const candidate of candidates) {
                const resolved = resolveActionMessageFromText(candidate);
                if (resolved) {
                    return resolved;
                }
            }

            return null;
        }

        function rememberActionMessage(message) {
            if (!message) {
                return;
            }

            lastActionMessage = message;
            lastActionAt = Date.now();
        }

        function consumeRecentActionMessage() {
            if (!lastActionMessage) {
                return null;
            }

            if ((Date.now() - lastActionAt) > ACTION_HINT_WINDOW_MS) {
                lastActionMessage = '';
                lastActionAt = 0;
                return null;
            }

            const message = lastActionMessage;
            lastActionMessage = '';
            lastActionAt = 0;
            return message;
        }

        function showActionMessage(message) {
            if (!message) {
                return;
            }

            rememberActionMessage(message);
            show({
                type: 'info',
                message,
            });
        }

        function isSubmitControl(trigger) {
            if (!trigger || typeof trigger.tagName !== 'string') {
                return false;
            }

            const tagName = trigger.tagName.toUpperCase();
            if (tagName === 'BUTTON') {
                return String(trigger.getAttribute('type') || 'submit').toLowerCase() === 'submit';
            }

            if (tagName === 'INPUT') {
                return String(trigger.getAttribute('type') || '').toLowerCase() === 'submit';
            }

            return false;
        }

        function clearTimers() {
            if (hideTimer) {
                window.clearTimeout(hideTimer);
                hideTimer = null;
            }

            if (removeTimer) {
                window.clearTimeout(removeTimer);
                removeTimer = null;
            }
        }

        function removeActiveToast(immediate = false) {
            clearTimers();

            if (!activeToast) {
                return;
            }

            const toast = activeToast;
            activeToast = null;

            const finalize = () => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            };

            if (immediate) {
                finalize();
                return;
            }

            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('-translate-y-2', 'opacity-0');
            removeTimer = window.setTimeout(finalize, 180);
        }

        function clear() {
            removeActiveToast(false);
        }

        function show(options = {}) {
            if (!host) {
                return;
            }

            const message = String(options.message ?? '').trim();
            if (message === '') {
                return;
            }

            const typeKey = String(options.type || 'info').toLowerCase();
            const meta = TYPE_META[typeKey] || TYPE_META.info;
            const durationMs = Number.isFinite(Number(options.durationMs)) ? Number(options.durationMs) : 3500;

            removeActiveToast(true);

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex w-full max-w-xl items-start gap-3 rounded-2xl border px-4 py-3 shadow-2xl backdrop-blur transition-all duration-200 ease-out ${meta.tone} -translate-y-2 opacity-0`;
            toast.setAttribute('role', meta.role);
            toast.setAttribute('aria-live', meta.live);
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = `
                <div class="mt-0.5">${meta.icon}</div>
                <div class="min-w-0 flex-1">
                    <p class="sr-only">${meta.label}</p>
                    <p class="text-sm font-medium leading-6">${escapeHtml(message)}</p>
                </div>
                <button type="button" class="shrink-0 rounded-full p-1 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Dismiss notification">
                    <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            toast.querySelector('button')?.addEventListener('click', clear);

            host.appendChild(toast);
            activeToast = toast;

            window.requestAnimationFrame(() => {
                toast.classList.remove('-translate-y-2', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            hideTimer = window.setTimeout(() => {
                clear();
            }, durationMs);
        }

        window.PMSnackbar = {
            show,
            clear,
            hasActive() {
                return Boolean(activeToast);
            },
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('button, input[type="submit"]');
            if (!trigger || trigger.disabled || trigger.dataset.snackbarIgnore === 'true') {
                return;
            }

            const form = trigger.form || trigger.closest('form');
            const message = resolveActionMessage(trigger, form);
            if (!message) {
                return;
            }

            rememberActionMessage(message);

            if (!isSubmitControl(trigger)) {
                showActionMessage(message);
            }
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || form.dataset.snackbarIgnore === 'true') {
                return;
            }

            const message = resolveActionMessage(event.submitter, form) || consumeRecentActionMessage();
            if (message) {
                showActionMessage(message);
            }
        }, true);

        const nativeSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function pmsSnackbarSubmitProxy() {
            if (this.dataset?.snackbarIgnore !== 'true') {
                const message = resolveActionMessage(document.activeElement, this) || consumeRecentActionMessage() || resolveActionMessage(null, this);
                if (message) {
                    showActionMessage(message);
                }
            }

            return nativeSubmit.call(this);
        };

        const initialMessage = @json($pmsSnackbarInitial);
        if (initialMessage && initialMessage.message) {
            show(initialMessage);
        }
    })();
</script>
