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
