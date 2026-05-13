<?php

?>
        <footer class="bg-white border-t border-gray-200 p-5 text-center text-sm text-gray-500 mt-auto">
            &copy; <?= date('Y') ?> STIKES Mitra Ria Husada Jakarta. Dashboard System (PHP Native).
        </footer>
    </main>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');

            const toggleFn = () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            };

            if (toggleBtn) toggleBtn.addEventListener('click', toggleFn);
            if (closeBtn) closeBtn.addEventListener('click', toggleFn);
            if (overlay) overlay.addEventListener('click', toggleFn);
        });

        window.showToast = function(msg, type='success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgClass = type === 'success' ? 'border-brand-teal text-brand-teal' : 'border-red-500 text-red-500';
            const iconHTML = type === 'success'
                ? '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                : '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

            toast.className = `flex items-center p-4 bg-white border-l-4 ${bgClass} shadow-xl rounded-lg transform translate-y-10 opacity-0 transition-all duration-300 min-w-[300px] pointer-events-auto`;

            toast.innerHTML = `
                ${iconHTML}
                <div class="font-medium text-sm ml-1 w-full text-gray-800">${msg}</div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-900 ml-3">✕</button>
            `;

            container.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };
    </script>

    <?php display_flash_message(); ?>
</body>
</html>
