/**
 * Simple Toast - Sistem Management RT
 * Provides window.Toast.success / error / info
 * No dependencies, Tailwind styled
 */
(function () {
    const CONTAINER_ID = 'toast-container';
    const DEFAULT_DURATION = 4000;

    function getContainer() {
        let c = document.getElementById(CONTAINER_ID);
        if (!c) {
            c = document.createElement('div');
            c.id = CONTAINER_ID;
            c.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(c);
        }
        return c;
    }

    function show(message, type = 'info', opts = {}) {
        const duration = opts.duration || (type === 'error' ? 5000 : DEFAULT_DURATION);
        const container = getContainer();

        const icons = {
            success: 'bi-check-circle-fill text-emerald-500',
            error: 'bi-x-circle-fill text-red-500',
            info: 'bi-info-circle-fill text-sky-500',
            warning: 'bi-exclamation-triangle-fill text-amber-500'
        };

        const bg = {
            success: 'bg-white border-emerald-200',
            error: 'bg-white border-red-200',
            info: 'bg-white border-slate-200',
            warning: 'bg-white border-amber-200'
        };

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto min-w-[280px] max-w-[420px] rounded-lg border shadow-lg px-4 py-3 flex items-start gap-3 bg-white ${bg[type] || bg.info} animate-[slideIn_0.2s_ease]`;
        toast.style.animation = 'slideIn 0.2s ease';
        toast.innerHTML = `
            <i class="bi ${icons[type] || icons.info} text-lg leading-none mt-0.5"></i>
            <div class="flex-1 text-sm text-slate-800 leading-snug">${escapeHtml(String(message))}</div>
            <button class="text-slate-400 hover:text-slate-600 -mr-1 p-1" aria-label="tutup"><i class="bi bi-x-lg text-xs"></i></button>
        `;
        const closeBtn = toast.querySelector('button');
        let timer = setTimeout(() => dismiss(), duration);
        function dismiss() {
            clearTimeout(timer);
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            toast.style.transition = 'all 0.2s ease';
            setTimeout(() => toast.remove(), 220);
        }
        closeBtn.addEventListener('click', dismiss);
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => timer = setTimeout(dismiss, 1800));
        container.appendChild(toast);
    }

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    window.Toast = {
        success: (m, o) => show(m, 'success', o || {}),
        error: (m, o) => show(m, 'error', o || {}),
        info: (m, o) => show(m, 'info', o || {}),
        warning: (m, o) => show(m, 'warning', o || {}),
        show
    };

    // inject keyframes once
    if (!document.getElementById('toast-keyframes')) {
        const s = document.createElement('style');
        s.id = 'toast-keyframes';
        s.textContent = '@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}';
        document.head.appendChild(s);
    }
})();
