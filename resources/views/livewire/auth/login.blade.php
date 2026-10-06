<div class="min-h-screen flex flex-col justify-between w-full">



  <!-- Main Container -->
  <main class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-center flex-1">

    <!-- Wrapper Card: Adapts between Split Mode and Centered Mode -->
    <div id="login-container" class="w-full max-w-md mx-auto lg:max-w-none bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-200/80 overflow-hidden grid lg:grid-cols-12 transition-all duration-300">
      
      <!-- LEFT HERO PANEL (Visible on Desktop, hidden on Mobile) -->
      <aside id="left-panel" class="hidden lg:flex lg:col-span-5 bg-[#0f172a] text-white p-8 lg:p-10 flex-col justify-center relative overflow-hidden" style="background: radial-gradient(circle at 100% 100%, rgba(30, 58, 138, 0.7) 0%, rgba(6, 182, 212, 0.2) 25%, transparent 60%), radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.25) 0%, transparent 45%), linear-gradient(135deg, #0f172a 0%, #0f172a 45%, #1e3a8a 100%);">
        
        <!-- Ambient Decorative Glows -->
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-cyan-400/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Upper Content -->
        <div class="relative z-10">
          <img src="{{ asset('icons/Logo Sarana - Putih.svg') }}" alt="Sarana" class="object-contain h-9 mb-4">

          <p class="text-lg font-regular text-white">
            Kelola Aset &amp; Layanan Kantor Lebih Cepat.
          </p>
        </div>

        <!-- Feature Highlights Grid -->
        <div class="relative z-10 my-8 space-y-3">
          <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
            <div class="h-9 w-9 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-300 shrink-0">
              <!-- Car / Kendaraan Dinas Icon -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l1.5-4.5A2 2 0 016.4 5h11.2a2 2 0 011.9 1.5L21 11m-18 0h18m-18 0v5a1 1 0 001 1h1m16-6v5a1 1 0 01-1 1h-1m-12 0h8"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-white">Peminjaman Kendaraan Dinas</p>
              <p class="text-xs text-slate-300">Cek ketersediaan dan booking tanpa antre</p>
            </div>
          </div>

          <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
            <div class="h-9 w-9 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-300 shrink-0">
              <!-- Clipboard Check / Approval Izin Pegawai Icon -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-white">Perizinan Keluar Pegawai</p>
              <p class="text-xs text-slate-300">Persetujuan berjenjang oleh atasan</p>
            </div>
          </div>

          <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
            <div class="h-9 w-9 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-300 shrink-0">
              <!-- Mail / Surat Keluar Icon -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-white">Administrasi Surat Keluar</p>
              <p class="text-xs text-slate-300">Manajemen Permintaan Surat Keluar</p>
            </div>
          </div>

          <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
            <div class="h-9 w-9 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-300 shrink-0">
              <!-- Pencil / Alat Tulis Kantor (ATK) Icon -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-white">Permintaan ATK</p>
              <p class="text-xs text-slate-300">Distribusi perlengkapan kantor transparan</p>
            </div>
          </div>
        </div>

      </aside>

      <!-- RIGHT FORM PANEL -->
      <section id="right-panel" class="lg:col-span-7 p-8 sm:p-12 lg:p-14 flex flex-col justify-center bg-white">
        
        <!-- Form Header Area -->
        <div>
          <!-- Header Banner -->
          <div class="mb-8">
            <h1 class="block lg:hidden text-3xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Sarana</h1>
            <h2 class="hidden lg:block text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-slate-500 text-sm mt-3">Masukkan NIP Pendek dan kata sandi akun Anda untuk melanjutkan.</p>
          </div>

          <!-- Error Alert Banner -->
          @if ($loginError)
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start gap-3 transition-all">
              <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs sm:text-sm font-bold text-rose-900">Autentikasi Gagal</p>
                <p class="text-xs sm:text-sm text-rose-700 mt-0.5 leading-relaxed">{{ $loginError }}</p>
              </div>
            </div>
          @endif

          <!-- Login Form -->
          <form id="loginForm" wire:submit.prevent="authenticate" class="space-y-5">
            
            <!-- NIP Input Field -->
            <div>
              <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                NIP Pendek / ID Pegawai
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#2d7dbe] transition">
                  <!-- User Icon -->
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <input 
                  type="text" 
                  id="nip" 
                  name="nip" 
                  wire:model="nip_pendek"
                  required 
                  autocomplete="username"
                  placeholder="Contoh: 19940822"
                  class="w-full pl-11 pr-4 py-3 bg-slate-50/70 hover:bg-slate-50 focus:bg-white text-slate-900 placeholder-slate-400 text-sm font-medium rounded-xl border border-slate-200 focus:border-[#2d7dbe] focus:ring-4 focus:ring-[#2d7dbe]/15 outline-none transition duration-200 shadow-2xs"
                />
              </div>
              @error('nip_pendek')
                <p class="text-xs font-semibold text-red-600 mt-1.5 flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Password Input Field -->
            <div>
              <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Password
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#2d7dbe] transition">
                  <!-- Lock Icon -->
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <input 
                  type="password" 
                  id="password" 
                  name="password" 
                  wire:model="password"
                  required 
                  autocomplete="current-password"
                  placeholder="••••••••••••"
                  class="w-full pl-11 pr-11 py-3 bg-slate-50/70 hover:bg-slate-50 focus:bg-white text-slate-900 placeholder-slate-400 text-sm font-medium rounded-xl border border-slate-200 focus:border-[#2d7dbe] focus:ring-4 focus:ring-[#2d7dbe]/15 outline-none transition duration-200 shadow-2xs"
                />
                <!-- Toggle Password Visibility -->
                <button 
                  type="button" 
                  id="togglePasswordBtn"
                  onclick="togglePasswordVisibility()" 
                  aria-label="Tampilkan atau sembunyikan password"
                  class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition cursor-pointer"
                >
                  <!-- Eye Icon (Default) -->
                  <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <!-- Eye Slash Icon (Hidden) -->
                  <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                  </svg>
                </button>
              </div>
              @error('password')
                <p class="text-xs font-semibold text-red-600 mt-1.5 flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Submit Button -->
            <div>
              <button 
                type="submit" 
                id="submitBtn"
                wire:loading.attr="disabled"
                class="w-full mt-5 py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 flex items-center justify-center gap-2 transition cursor-pointer group disabled:opacity-80 disabled:cursor-not-allowed"
              >
                <span id="btnText" wire:loading.remove wire:target="authenticate">Masuk Sekarang</span>
                <span wire:loading wire:target="authenticate">Memverifikasi...</span>

                <!-- Arrow Right Icon -->
                <svg id="btnIcon" wire:loading.remove wire:target="authenticate" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>

                <!-- Loading Spinner -->
                <svg id="btnSpinner" wire:loading wire:target="authenticate" class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
              </button>
            </div>
          </form>

        </div>

      </section>
    </div>
  </main>

  <!-- Bottom Simple Status Bar -->
    <footer class="w-full text-center py-4 text-xs text-slate-400 font-medium">
      &copy; {{ date('Y') }} <a href="https://dhafinfuad.netlify.app" target="_blank" rel="noopener noreferrer" class="hover:text-brand-600 transition-colors decoration-slate-300 underline-offset-2 hover:decoration-brand-600">Dhafin Fuad Mahathir</a> &bull; KPP Madya Malang
    </footer>

  <!-- Toast Notification Element -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-16 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-3 p-4 rounded-2xl shadow-xl border bg-white max-w-sm">
    <div id="toast-icon-wrapper" class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
      <!-- Injected via JS -->
    </div>
    <div class="text-xs">
      <p id="toast-title" class="font-bold text-slate-800"></p>
      <p id="toast-desc" class="text-slate-500 mt-0.5"></p>
    </div>
  </div>



  <script>
    // Toggle Password Visibility
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eye-icon');
      const eyeSlashIcon = document.getElementById('eye-slash-icon');

      if (!passwordInput) return;

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (eyeIcon) eyeIcon.classList.add('hidden');
        if (eyeSlashIcon) eyeSlashIcon.classList.remove('hidden');
      } else {
        passwordInput.type = 'password';
        if (eyeIcon) eyeIcon.classList.remove('hidden');
        if (eyeSlashIcon) eyeSlashIcon.classList.add('hidden');
      }
    }

    // Switch between Split Screen and Centered Card view
    function switchLayout(layout) {
      const container = document.getElementById('login-container');
      const leftPanel = document.getElementById('left-panel');
      const rightPanel = document.getElementById('right-panel');
      const btnSplit = document.getElementById('btn-split-layout');
      const btnCard = document.getElementById('btn-card-layout');

      if (layout === 'card') {
        // Transform into a compact centered single card
        if (container) {
          container.classList.remove('max-w-6xl', 'grid', 'lg:grid-cols-12');
          container.classList.add('max-w-lg');
        }
        if (leftPanel) leftPanel.classList.add('hidden');
        if (rightPanel) {
          rightPanel.classList.remove('lg:col-span-7');
          rightPanel.classList.add('w-full');
        }

        // Style buttons
        if (btnCard) btnCard.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#2d7dbe] bg-[#2d7dbe]/10 transition cursor-pointer';
        if (btnSplit) btnSplit.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition cursor-pointer';
        showToast('Beralih ke tampilan Centered Card', 'info');
      } else {
        // Transform back into dual split layout
        if (container) {
          container.classList.remove('max-w-lg');
          container.classList.add('max-w-6xl', 'grid', 'lg:grid-cols-12');
        }
        if (leftPanel) leftPanel.classList.remove('hidden');
        if (rightPanel) {
          rightPanel.classList.add('lg:col-span-7');
          rightPanel.classList.remove('w-full');
        }

        // Style buttons
        if (btnSplit) btnSplit.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#2d7dbe] bg-[#2d7dbe]/10 transition cursor-pointer';
        if (btnCard) btnCard.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition cursor-pointer';
        showToast('Beralih ke tampilan Split Hero', 'info');
      }
    }

    // Fill Demo Data for fast testing
    function fillDemoData() {
      const nip = document.getElementById('nip');
      const pass = document.getElementById('password');
      if (nip) {
        nip.value = '19940822';
        nip.dispatchEvent(new Event('input', { bubbles: true }));
      }
      if (pass) {
        pass.value = 'sarana2026';
        pass.dispatchEvent(new Event('input', { bubbles: true }));
      }

      // Sync with Livewire if initialized
      try {
        if (window.Livewire) {
          const form = document.getElementById('loginForm');
          const wireId = form ? form.closest('[wire\\:id]')?.getAttribute('wire:id') : null;
          if (wireId) {
            const comp = window.Livewire.find(wireId);
            if (comp) {
              comp.set('nip_pendek', '19940822');
              comp.set('password', 'sarana2026');
            }
          }
        }
      } catch (err) {
        console.error('Livewire sync error:', err);
      }

      showToast('Kredensial demo berhasil dimasukkan!', 'success');
    }



    // Toast Notification System
    let toastTimeout;
    function showToast(message, type = 'info') {
      const toast = document.getElementById('toast');
      const toastTitle = document.getElementById('toast-title');
      const toastDesc = document.getElementById('toast-desc');
      const iconWrapper = document.getElementById('toast-icon-wrapper');
      if (!toast || !toastTitle || !toastDesc || !iconWrapper) return;

      clearTimeout(toastTimeout);

      if (type === 'success') {
        toastTitle.textContent = 'Berhasil';
        iconWrapper.className = 'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-200';
        iconWrapper.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`;
      } else if (type === 'error') {
        toastTitle.textContent = 'Terjadi Kesalahan';
        iconWrapper.className = 'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-rose-50 text-rose-600 border border-rose-200';
        iconWrapper.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>`;
      } else if (type === 'warning') {
        toastTitle.textContent = 'Perhatian';
        iconWrapper.className = 'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-200';
        iconWrapper.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
      } else {
        toastTitle.textContent = 'Informasi';
        iconWrapper.className = 'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-200';
        iconWrapper.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
      }

      toastDesc.textContent = message;
      toast.classList.remove('translate-y-16', 'opacity-0', 'pointer-events-none');
      toast.classList.add('translate-y-0', 'opacity-100');

      toastTimeout = setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-16', 'opacity-0', 'pointer-events-none');
      }, 3500);
    }
  </script>
</div>
