<!-- Modal Ubah Password Global -->
<div id="modal-ubah-password-global" wire:ignore
    data-modal-container
    class="app-modal-container hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex justify-center items-center p-4">
    <div x-data="passwordValidator()"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden text-left border border-slate-100">
        
        <!-- Modal Header -->
        <div class="px-6 sm:px-7 py-4 border-b border-slate-100 flex justify-between items-center bg-white flex-shrink-0">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-brand-600">lock_reset</span>
                <span>Ubah Password Akun</span>
            </h2>
            <button type="button" onclick="toggleModalPasswordGlobal('modal-ubah-password-global')"
                class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer hover:bg-slate-100 rounded-lg" title="Tutup">
                &times;
            </button>
        </div>

        <!-- Form Ubah Password -->
        <form id="form-ubah-password-global" action="{{ route('password.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 sm:p-7 bg-white space-y-4 text-xs">
                <!-- Password Lama -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Saat Ini
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" required
                            x-model="currentPassword" @input.debounce.500ms="checkCurrentPassword"
                            placeholder="Masukkan password lama"
                            class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition">
                    </div>
                    <span x-show="currentPasswordError" x-text="currentPasswordError" class="text-rose-600 text-[11px] mt-1.5 block font-semibold" style="display: none;"></span>
                    <span x-show="currentPasswordValid" class="text-emerald-600 text-[11px] mt-1.5 block font-semibold" style="display: none;">Password lama benar.</span>
                    @error('current_password')
                        <span x-show="!currentPasswordError && !currentPasswordValid" class="text-rose-600 text-[11px] mt-1.5 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Baru (Minimal 6 Karakter)
                    </label>
                    <input type="password" name="password" required
                        x-model="newPassword" @input="validateNewPassword"
                        placeholder="••••••••••••"
                        class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition">
                    <span x-show="newPasswordError" x-text="newPasswordError" class="text-rose-600 text-[11px] mt-1.5 block font-semibold" style="display: none;"></span>
                    @error('password')
                        <span x-show="!newPasswordError" class="text-rose-600 text-[11px] mt-1.5 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Konfirmasi Ulang Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required
                            x-model="confirmPassword" @input="validateConfirmPassword"
                            placeholder="Ulangi password baru"
                            class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition">
                    </div>
                    <span x-show="confirmPasswordError" x-text="confirmPasswordError" class="text-rose-600 text-[11px] mt-1.5 block font-semibold" style="display: none;"></span>
                    <span x-show="confirmPasswordValid" class="text-emerald-600 text-[11px] mt-1.5 block font-semibold" style="display: none;">Password baru cocok.</span>
                </div>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="px-6 sm:px-7 py-4 border-t border-slate-100 bg-white flex justify-end gap-2.5 w-full flex-shrink-0">
            <button type="button" onclick="toggleModalPasswordGlobal('modal-ubah-password-global')"
                class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                Batal
            </button>
            <button type="submit" form="form-ubah-password-global"
                :disabled="currentPasswordError !== '' || newPasswordError !== '' || confirmPasswordError !== '' || currentPassword === '' || newPassword === '' || confirmPassword === ''"
                class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan Password
            </button>
        </div>
    </div>
</div>

<script>
    // Simpan state modal di window agar tahan terhadap Livewire re-render/morph
    if (typeof window._passwordModalOpen === 'undefined') {
        window._passwordModalOpen = false;
    }

    if (typeof window.toggleModalPasswordGlobal === 'undefined') {
        window.toggleModalPasswordGlobal = function(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                const isHidden = modal.classList.contains('hidden');
                if (isHidden) {
                    modal.classList.remove('hidden');
                    window._passwordModalOpen = true;
                } else {
                    modal.classList.add('hidden');
                    window._passwordModalOpen = false;
                }
            } else {
                console.error('Modal with ID ' + modalID + ' not found.');
            }
        }
    }

    // Setelah Livewire selesai update DOM, pulihkan state modal jika sedang terbuka
    document.addEventListener('livewire:navigated', restorePasswordModal);
    document.addEventListener('livewire:updated', restorePasswordModal);

    function restorePasswordModal() {
        if (window._passwordModalOpen) {
            const modal = document.getElementById('modal-ubah-password-global');
            if (modal && modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            }
        }
    }

    function passwordValidator() {
        return {
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            currentPasswordError: '',
            currentPasswordValid: false,
            newPasswordError: '',
            confirmPasswordError: '',
            confirmPasswordValid: false,

            async checkCurrentPassword() {
                if (this.currentPassword.length === 0) {
                    this.currentPasswordError = '';
                    this.currentPasswordValid = false;
                    return;
                }
                
                try {
                    const response = await fetch('{{ route('password.check') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ password: this.currentPassword })
                    });
                    const data = await response.json();
                    
                    if (!data.valid) {
                        this.currentPasswordError = 'Password lama yang Anda masukkan salah.';
                        this.currentPasswordValid = false;
                    } else {
                        this.currentPasswordError = '';
                        this.currentPasswordValid = true;
                    }
                } catch (error) {
                    console.error('Error checking password:', error);
                }
            },

            validateNewPassword() {
                if (this.newPassword.length > 0 && this.newPassword.length < 6) {
                    this.newPasswordError = 'Password baru Anda kurang dari 6 karakter.';
                } else {
                    this.newPasswordError = '';
                }
                
                // Re-validate confirm password if it's already filled
                if (this.confirmPassword.length > 0) {
                    this.validateConfirmPassword();
                }
            },

            validateConfirmPassword() {
                if (this.confirmPassword.length === 0) {
                    this.confirmPasswordError = '';
                    this.confirmPasswordValid = false;
                    return;
                }
                
                if (this.confirmPassword !== this.newPassword) {
                    this.confirmPasswordError = 'Password baru yang Anda masukkan tidak sesuai/sama.';
                    this.confirmPasswordValid = false;
                } else if (this.newPasswordError === '') {
                    this.confirmPasswordError = '';
                    this.confirmPasswordValid = true;
                }
            }
        }
    }
</script>