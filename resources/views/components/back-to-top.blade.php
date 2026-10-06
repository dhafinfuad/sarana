<!-- Floating Back to Top Button -->
<div id="back-to-top-wrapper"
     style="position: fixed !important; bottom: 80px !important; right: 24px !important; z-index: 45 !important;">
    <button type="button"
            id="btn-back-to-top"
            onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); document.documentElement.scrollTo({ top: 0, behavior: 'smooth' }); document.body.scrollTo({ top: 0, behavior: 'smooth' });"
            style="width: 48px !important; height: 48px !important; border-radius: 9999px !important; background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.45), 0 8px 10px -6px rgba(37, 99, 235, 0.3) !important; border: 1px solid rgba(255, 255, 255, 0.25) !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.25s ease !important; outline: none !important; opacity: 0; pointer-events: none; transform: translateY(16px) scale(0.9);"
            class="hover:brightness-110 active:scale-95"
            title="Kembali ke atas">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none !important;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</div>

<style>
    #btn-back-to-top:hover {
        background-color: #1d4ed8 !important;
        box-shadow: 0 14px 30px -4px rgba(29, 78, 216, 0.55), 0 10px 12px -5px rgba(29, 78, 216, 0.35) !important;
        transform: translateY(-2px) scale(1.05) !important;
    }
    #btn-back-to-top:active {
        background-color: #1e40af !important;
        transform: translateY(0) scale(0.95) !important;
    }

    /* Sembunyikan seketika via CSS ketika ada modal atau drawer pop-up yang sedang aktif/terbuka */
    body:has([data-modal-container]:not([style*="display: none"]):not([style*="display:none"]):not(.hidden)) #back-to-top-wrapper,
    body:has(.app-modal-container:not([style*="display: none"]):not([style*="display:none"]):not(.hidden)) #back-to-top-wrapper,
    body:has(#modal-ubah-password-global:not(.hidden)) #back-to-top-wrapper,
    body:has(#mobile-drawer:not([style*="display: none"]):not([style*="display:none"])) #back-to-top-wrapper {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transform: translateY(16px) scale(0.9) !important;
    }
</style>

<script>
(function() {
    function isModalOpen() {
        // 1. Cek semua modal dengan atribut [data-modal-container] atau class .app-modal-container
        var modals = document.querySelectorAll('[data-modal-container], .app-modal-container, #modal-ubah-password-global');
        for (var i = 0; i < modals.length; i++) {
            var m = modals[i];
            if (m.classList.contains('hidden')) continue;
            if (m.style.display === 'none') continue;
            var comp = window.getComputedStyle(m);
            if (comp.display !== 'none' && comp.visibility !== 'hidden') {
                return true;
            }
        }

        // 2. Cek elemen overlay fixed lainnya dengan z-index >= 50
        var overlays = document.querySelectorAll('.fixed.inset-0:not(#back-to-top-wrapper)');
        for (var j = 0; j < overlays.length; j++) {
            var ov = overlays[j];
            if (ov.id === 'back-to-top-wrapper' || ov.closest('#back-to-top-wrapper')) continue;
            if (ov.classList.contains('hidden') || ov.style.display === 'none') continue;
            var c = window.getComputedStyle(ov);
            if (c.display !== 'none' && c.visibility !== 'hidden') {
                var z = parseInt(c.zIndex, 10);
                if (!isNaN(z) && z >= 50) {
                    return true;
                }
            }
        }

        return false;
    }

    function handleBackToTopScroll() {
        var btn = document.getElementById("btn-back-to-top");
        var wrapper = document.getElementById("back-to-top-wrapper");
        if (!btn || !wrapper) return;

        // Jika ada modal terbuka, sembunyikan tombol
        if (isModalOpen()) {
            btn.style.opacity = "0";
            btn.style.pointerEvents = "none";
            btn.style.transform = "translateY(16px) scale(0.9)";
            wrapper.style.pointerEvents = "none";
            return;
        }

        var scrollPos = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
        if (scrollPos > 30) {
            btn.style.opacity = "1";
            btn.style.pointerEvents = "auto";
            wrapper.style.pointerEvents = "auto";
            if (!btn.matches(':hover')) {
                btn.style.transform = "translateY(0) scale(1)";
            }
        } else {
            btn.style.opacity = "0";
            btn.style.pointerEvents = "none";
            btn.style.transform = "translateY(16px) scale(0.9)";
            wrapper.style.pointerEvents = "none";
        }
    }

    // Expose global helper jika dibutuhkan
    window.checkBackToTopVisibility = handleBackToTopScroll;

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", handleBackToTopScroll);
    } else {
        handleBackToTopScroll();
    }

    window.addEventListener("scroll", handleBackToTopScroll, { passive: true, capture: true });
    document.addEventListener("scroll", handleBackToTopScroll, { passive: true, capture: true });
    document.addEventListener("livewire:navigated", handleBackToTopScroll);
    document.addEventListener("livewire:initialized", handleBackToTopScroll);

    // Observer mutasi DOM untuk mendeteksi pembukaan/penutupan modal
    if (window.MutationObserver) {
        var observer = new MutationObserver(function() {
            handleBackToTopScroll();
        });
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['style', 'class'],
            subtree: true
        });
    }
})();
</script>
