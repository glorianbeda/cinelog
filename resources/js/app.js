import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Interactive Star Rating Component for Alpine
Alpine.data('starRating', (initialValue = 0, inputName = 'rating_overall') => ({
    rating: parseFloat(initialValue) || 0,
    hoverRating: 0,
    name: inputName,
    isPopping: false,

    init() {
        window.addEventListener('set-rating', (e) => {
            if (e.detail && e.detail.name === this.name) {
                this.rating = parseFloat(e.detail.value) || 0;
            }
        });
    },

    get currentDisplayRating() {
        return this.hoverRating > 0 ? this.hoverRating : this.rating;
    },

    get ratingLabel() {
        const r = this.currentDisplayRating;
        if (r === 0) return 'Pilih Rating';
        if (r <= 1.0) return '💀 Unwatchable / Terrible';
        if (r <= 2.0) return '👎 Bad / Disappointing';
        if (r <= 3.0) return '😐 Decent / Mediocre';
        if (r <= 4.0) return '👍 Good / Recommended';
        if (r <= 4.5) return '🔥 Great / High Praise';
        return '🏆 Masterpiece / Mahakarya';
    },

    get badgeColorClass() {
        const r = this.currentDisplayRating;
        if (r === 0) return 'bg-zinc-800 text-zinc-400 border-zinc-700';
        if (r <= 1.0) return 'bg-rose-500 text-white border-white/30 shadow-[2px_2px_0px_#fff]';
        if (r <= 2.0) return 'bg-orange-500 text-black border-white/30 shadow-[2px_2px_0px_#fff]';
        if (r <= 3.0) return 'bg-amber-400 text-black border-white/30 shadow-[2px_2px_0px_#fff]';
        if (r <= 4.0) return 'bg-emerald-400 text-black border-white/30 shadow-[2px_2px_0px_#fff]';
        if (r <= 4.5) return 'bg-cyan-400 text-black border-white/30 shadow-[2px_2px_0px_#fff]';
        return 'bg-yellow-400 text-black border-white/40 shadow-[2px_2px_0px_#A855F7] animate-pulse';
    },

    setHover(starIndex, isHalf) {
        this.hoverRating = starIndex - (isHalf ? 0.5 : 0);
    },

    clearHover() {
        this.hoverRating = 0;
    },

    selectRating(starIndex, isHalf) {
        this.rating = starIndex - (isHalf ? 0.5 : 0);
        this.hoverRating = 0;
        this.isPopping = true;
        setTimeout(() => this.isPopping = false, 300);
    },

    reset() {
        this.rating = 0;
        this.hoverRating = 0;
    },

    getStarState(starIndex) {
        const current = this.currentDisplayRating;
        if (current >= starIndex) return 'full';
        if (current >= starIndex - 0.5) return 'half';
        return 'empty';
    }
}));

// Form Draft Auto-Save & Recovery Component for Alpine
Alpine.data('formDraft', (storageKey, options = {}) => ({
    storageKey: storageKey,
    hasDraft: false,
    savedAt: '',
    saveStatus: 'idle', // 'idle' | 'saving' | 'saved'
    saveTimeout: null,
    statusTimeout: null,
    toastMessage: '',

    init() {
        this.checkExistingDraft();

        // Listen for input/change on form to auto-save with debounce
        this.$el.addEventListener('input', () => this.debouncedSave());
        this.$el.addEventListener('change', () => this.debouncedSave());

        // On form submit, clear draft so next load won't show stale draft
        this.$el.addEventListener('submit', () => {
            this.clearDraft();
        });
    },

    checkExistingDraft() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (parsed && parsed.data && Object.keys(parsed.data).length > 0) {
                // Ensure at least one non-empty value
                const hasValue = Object.values(parsed.data).some(v => v !== '' && v !== null && v !== false && (Array.isArray(v) ? v.length > 0 : true));
                if (hasValue) {
                    this.hasDraft = true;
                    this.savedAt = parsed.savedAt || 'beberapa saat lalu';
                }
            }
        } catch (e) {
            console.error('Error reading form draft:', e);
        }
    },

    debouncedSave() {
        this.saveStatus = 'saving';
        clearTimeout(this.saveTimeout);
        this.saveTimeout = setTimeout(() => {
            this.saveDraft();
        }, 600);
    },

    saveDraft() {
        try {
            const data = {};
            const elements = this.$el.querySelectorAll('input, select, textarea');

            elements.forEach(el => {
                const name = el.name || el.id;
                if (!name) return;

                // Skip CSRF, method, and password fields for security
                if (name === '_token' || name === '_method' || el.type === 'password') {
                    return;
                }

                if (el.type === 'checkbox') {
                    data[name] = el.checked;
                } else if (el.type === 'radio') {
                    if (el.checked) {
                        data[name] = el.value;
                    }
                } else if (name === 'genres[]') {
                    if (!data['genres']) data['genres'] = [];
                    data['genres'].push(el.value);
                } else {
                    data[name] = el.value;
                }
            });

            // Also capture poster preview src if present
            const posterPreview = document.getElementById('poster_preview');
            if (posterPreview && posterPreview.src && !posterPreview.classList.contains('hidden')) {
                data['_poster_src'] = posterPreview.src;
            }

            // Only save if meaningful data is entered
            const keys = Object.keys(data);
            const hasMeaningfulData = keys.some(k => {
                const val = data[k];
                return val !== '' && val !== null && val !== false && (Array.isArray(val) ? val.length > 0 : true);
            });

            if (hasMeaningfulData) {
                const now = new Date();
                const savedAt = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                localStorage.setItem(this.storageKey, JSON.stringify({
                    data,
                    savedAt,
                    timestamp: now.getTime()
                }));
                this.hasDraft = true;
                this.savedAt = savedAt;
                this.saveStatus = 'saved';
            } else {
                this.saveStatus = 'idle';
            }

            clearTimeout(this.statusTimeout);
            this.statusTimeout = setTimeout(() => {
                this.saveStatus = 'idle';
            }, 2500);
        } catch (e) {
            console.error('Error saving form draft:', e);
            this.saveStatus = 'idle';
        }
    },

    restoreDraft() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (!parsed || !parsed.data) return;

            const data = parsed.data;

            // Restore elements
            Object.keys(data).forEach(key => {
                const val = data[key];

                // Handle star ratings
                if (['rating_overall', 'rating_story', 'rating_acting', 'rating_visual', 'rating_audio'].includes(key)) {
                    window.dispatchEvent(new CustomEvent('set-rating', {
                        detail: { name: key, value: parseFloat(val) || 0 }
                    }));
                }

                // Handle genres array
                if (key === 'genres' && Array.isArray(val)) {
                    const genreContainer = document.getElementById('genre_container');
                    if (genreContainer) {
                        genreContainer.innerHTML = '';
                        val.forEach(g => {
                            const badge = document.createElement('span');
                            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/40 rounded text-xs font-mono font-bold';
                            badge.innerHTML = `${g} <input type="hidden" name="genres[]" value="${g}">`;
                            genreContainer.appendChild(badge);
                        });
                    }
                    return;
                }

                // Handle poster preview
                if (key === '_poster_src' && val) {
                    const posterPreview = document.getElementById('poster_preview');
                    if (posterPreview) {
                        posterPreview.src = val;
                        posterPreview.classList.remove('hidden');
                    }
                    return;
                }

                // Find element by name or id
                const el = this.$el.querySelector(`[name="${key}"]`) || document.getElementById(key);
                if (el) {
                    if (el.type === 'checkbox') {
                        el.checked = Boolean(val);
                    } else if (el.type === 'radio') {
                        const radio = this.$el.querySelector(`[name="${key}"][value="${val}"]`);
                        if (radio) radio.checked = true;
                    } else {
                        el.value = val;
                    }
                    // Trigger events for any reactive bindings
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // If poster_url is filled, update poster preview
            if (data['poster_url']) {
                const posterPreview = document.getElementById('poster_preview');
                if (posterPreview) {
                    posterPreview.src = data['poster_url'];
                    posterPreview.classList.remove('hidden');
                }
            }

            this.toastMessage = 'Draf berhasil dipulihkan!';
            setTimeout(() => {
                this.toastMessage = '';
            }, 4000);
        } catch (e) {
            console.error('Error restoring form draft:', e);
            alert('Gagal memulihkan draf.');
        }
    },

    clearDraft() {
        try {
            localStorage.removeItem(this.storageKey);
            this.hasDraft = false;
            this.savedAt = '';
            this.saveStatus = 'idle';
        } catch (e) {
            console.error('Error clearing form draft:', e);
        }
    },

    discardDraft() {
        if (confirm('Apakah Anda yakin ingin membuang draf tersimpan di browser ini?')) {
            this.clearDraft();
            this.toastMessage = 'Draf telah dihapus.';
            setTimeout(() => {
                this.toastMessage = '';
            }, 3000);
        }
    }
}));

// TMDB Live Search & Auto-Fill Component
Alpine.data('tmdbSearcher', () => ({
    query: '',
    type: 'all',
    results: [],
    loading: false,
    hasKey: true,
    isOpen: false,
    selectedItem: null,
    errorMessage: '',

    async search() {
        if (!this.query || this.query.trim().length < 2) {
            this.results = [];
            this.isOpen = false;
            return;
        }

        this.loading = true;
        this.errorMessage = '';

        try {
            const url = `/admin/api/tmdb/search?q=${encodeURIComponent(this.query)}&type=${this.type}`;
            const res = await fetch(url);
            const data = await res.json();

            this.hasKey = data.has_key;
            this.results = data.results || [];
            this.isOpen = true;
        } catch (e) {
            this.errorMessage = 'Gagal melakukan pencarian TMDB.';
            this.results = [];
        } finally {
            this.loading = false;
        }
    },

    async selectResult(item) {
        this.loading = true;
        this.isOpen = false;
        this.query = item.title;

        try {
            const url = `/admin/api/tmdb/details/${item.type}/${item.tmdb_id}`;
            const res = await fetch(url);
            const details = await res.json();

            if (res.ok && details) {
                // Auto-fill the form inputs
                document.getElementById('input_title').value = details.title || '';
                if (document.getElementById('input_original_title')) {
                    document.getElementById('input_original_title').value = details.original_title || '';
                }
                document.getElementById('input_type').value = details.type || 'movie';
                document.getElementById('input_tmdb_id').value = details.tmdb_id || '';
                document.getElementById('input_release_year').value = details.release_year || '';
                if (document.getElementById('input_release_date')) {
                    document.getElementById('input_release_date').value = details.release_date || '';
                }
                document.getElementById('input_synopsis').value = details.synopsis || '';
                document.getElementById('input_poster_url').value = details.poster_url || '';
                document.getElementById('input_backdrop_url').value = details.backdrop_url || '';
                
                if (document.getElementById('input_director')) {
                    document.getElementById('input_director').value = details.director || '';
                }
                if (document.getElementById('input_runtime_minutes')) {
                    document.getElementById('input_runtime_minutes').value = details.runtime_minutes || '';
                }
                if (document.getElementById('input_total_seasons')) {
                    document.getElementById('input_total_seasons').value = details.total_seasons || '';
                }
                if (document.getElementById('input_total_episodes')) {
                    document.getElementById('input_total_episodes').value = details.total_episodes || '';
                }
                if (document.getElementById('input_cast_members')) {
                    document.getElementById('input_cast_members').value = JSON.stringify(details.cast_members || []);
                }

                // Update genre checkboxes/tags if present
                if (details.genres && Array.isArray(details.genres)) {
                    const genreContainer = document.getElementById('genre_container');
                    if (genreContainer) {
                        genreContainer.innerHTML = '';
                        details.genres.forEach(g => {
                            const badge = document.createElement('span');
                            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/40 rounded text-xs font-mono font-bold';
                            badge.innerHTML = `${g} <input type="hidden" name="genres[]" value="${g}">`;
                            genreContainer.appendChild(badge);
                        });
                    }
                }

                // Update Preview image
                const posterPreview = document.getElementById('poster_preview');
                if (posterPreview && details.poster_url) {
                    posterPreview.src = details.poster_url;
                    posterPreview.classList.remove('hidden');
                }

                this.selectedItem = details;
            }
        } catch (e) {
            console.error('Error fetching details:', e);
            alert('Gagal mengambil detail film dari TMDB.');
        } finally {
            this.loading = false;
        }
    }
}));

Alpine.start();
