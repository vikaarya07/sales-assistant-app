window.musicPlayer = {
    audio: null,
    currentId: null,
    currentTitle: "",
    initialized: false,

    animationFrame: null,
    playButtonBound: false,
    waveformClickBound: false,

    audioContext: null,

    waveformCache: new Map(),
    waveformQueue: [],
    waveformProcessing: false,
    waveformObserver: null,

    init() {
        if (!this.audio) {
            this.audio = new Audio();

            /*
             * Jangan gunakan "auto".
             *
             * metadata cukup untuk mendapatkan duration
             * tanpa langsung mengunduh seluruh file audio.
             */
            this.audio.preload = "metadata";

            this.bindAudioEvents();
        }

        this.initWaveforms();
        this.bindPlayButtons();
        this.bindWaveformClicks();

        this.initialized = true;
    },

    /*
    |--------------------------------------------------------------------------
    | AUDIO EVENTS
    |--------------------------------------------------------------------------
    */

    bindAudioEvents() {
        if (!this.audio) {
            return;
        }

        this.audio.addEventListener("loadedmetadata", () => {
            this.updateDuration();

            if (this.currentId) {
                this.updateTimeDisplay(
                    this.currentId,
                    this.audio.currentTime,
                    this.audio.duration,
                );
            }
        });

        this.audio.addEventListener("timeupdate", () => {
            if (!this.currentId) {
                return;
            }

            this.updateTimeDisplay(
                this.currentId,
                this.audio.currentTime,
                this.audio.duration,
            );

            this.updateWaveformProgress();
        });

        this.audio.addEventListener("ended", () => {
            if (this.currentId) {
                this.updateTimeDisplay(
                    this.currentId,
                    this.audio.duration || 0,
                    this.audio.duration || 0,
                );

                this.setWaveformProgress(this.currentId, 1);
                this.setButtonState(this.currentId, false);
            }

            this.stopAnimation();
        });

        this.audio.addEventListener("pause", () => {
            if (this.currentId) {
                this.setButtonState(this.currentId, false);
            }

            this.stopAnimation();
        });

        this.audio.addEventListener("play", () => {
            if (this.currentId) {
                this.setButtonState(this.currentId, true);
            }

            this.startAnimation();
        });

        this.audio.addEventListener("error", () => {
            console.error("Music player error:", this.audio?.error);

            if (this.currentId) {
                this.setButtonState(this.currentId, false);
            }

            this.stopAnimation();
        });
    },

    /*
    |--------------------------------------------------------------------------
    | PLAY BUTTON
    |--------------------------------------------------------------------------
    */

    bindPlayButtons() {
        if (this.playButtonBound) {
            return;
        }

        this.playButtonBound = true;

        document.addEventListener("click", (event) => {
            const button = event.target.closest("[data-music-play]");

            if (!button) {
                return;
            }

            event.preventDefault();

            this.toggle(button);
        });
    },

    async toggle(button) {
        const id = button.dataset.id;
        const url = button.dataset.url;
        const title = button.dataset.title || "";

        if (!id || !url) {
            return;
        }

        /*
         * Jika lagu yang sama sedang diputar,
         * cukup pause/play.
         */
        if (this.currentId === id && this.audio && this.audio.src) {
            if (this.audio.paused) {
                try {
                    await this.audio.play();
                } catch (error) {
                    console.error("Play failed:", error);
                }
            } else {
                this.audio.pause();
            }

            return;
        }

        /*
         * Lagu berbeda.
         */
        await this.play(id, url, title);
    },

    async play(id, url, title = "") {
        if (!this.audio) {
            this.init();
        }

        this.stopAnimation();

        /*
         * Reset state lagu sebelumnya.
         */
        if (this.currentId && this.currentId !== id) {
            this.setButtonState(this.currentId, false);
            this.setWaveformProgress(this.currentId, 0);
            this.resetTimeDisplay(this.currentId);
        }

        this.currentId = id;
        this.currentTitle = title;

        /*
         * Set source hanya ketika user benar-benar menekan Play.
         *
         * Ini bagian penting untuk mengurangi ukuran reload.
         */
        if (this.audio.src !== new URL(url, window.location.href).href) {
            this.audio.pause();

            this.audio.src = url;
            this.audio.load();
        }

        this.setButtonState(id, true);

        /*
         * Coba play langsung.
         *
         * Browser akan mengambil audio hanya ketika diperlukan.
         */
        try {
            await this.audio.play();
        } catch (error) {
            console.error("Music playback failed:", error);

            this.setButtonState(id, false);
            return;
        }

        /*
         * Waveform dibuat secara lazy.
         *
         * Tidak dilakukan sebelum user menekan Play.
         */
        this.queueWaveform(id, url);
    },

    /*
    |--------------------------------------------------------------------------
    | BUTTON STATE
    |--------------------------------------------------------------------------
    */

    setButtonState(id, playing) {
        const buttons = document.querySelectorAll(
            `[data-music-play][data-id="${CSS.escape(String(id))}"]`,
        );

        buttons.forEach((button) => {
            const playIcon = button.querySelector('[data-music-icon="play"]');

            const pauseIcon = button.querySelector('[data-music-icon="pause"]');

            if (playing) {
                playIcon?.classList.add("scale-0", "opacity-0");

                pauseIcon?.classList.remove("scale-0", "opacity-0");

                button.setAttribute(
                    "aria-label",
                    `Pause ${button.dataset.title || ""}`,
                );
            } else {
                playIcon?.classList.remove("scale-0", "opacity-0");

                pauseIcon?.classList.add("scale-0", "opacity-0");

                button.setAttribute(
                    "aria-label",
                    `Play ${button.dataset.title || ""}`,
                );
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | WAVEFORM
    |--------------------------------------------------------------------------
    */

    initWaveforms() {
        const elements = document.querySelectorAll("[data-waveform]");

        if (!elements.length) {
            return;
        }

        /*
         * Jangan decode audio ketika halaman pertama dibuka.
         *
         * Cukup tampilkan placeholder ringan.
         */
        elements.forEach((element) => {
            if (element.dataset.initialized) {
                return;
            }

            const url = element.dataset.url;

            if (!url) {
                return;
            }

            element.dataset.initialized = "true";

            this.renderPlaceholder(element);
        });
    },

    renderPlaceholder(element) {
        const count = this.getBarCount(element);

        element.innerHTML = `
            <div
                style="
                    display:flex;
                    align-items:center;
                    width:100%;
                    height:100%;
                    gap:1px;
                    overflow:hidden;
                "
            >
                ${Array.from(
                    { length: count },
                    () => `
                    <span
                        style="
                            flex:1 1 0%;
                            min-width:0;
                            height:20%;
                            border-radius:9999px;
                            background:#d4d4d8;
                            pointer-events:none;
                        "
                    ></span>
                `,
                ).join("")}
            </div>
        `;
    },

    /*
    |--------------------------------------------------------------------------
    | LAZY WAVEFORM QUEUE
    |--------------------------------------------------------------------------
    */

    queueWaveform(id, url) {
        if (!id || !url) {
            return;
        }

        const element = document.querySelector(
            `[data-waveform][data-id="${CSS.escape(String(id))}"]`,
        );

        if (!element) {
            return;
        }

        const count = this.getBarCount(element);
        const cacheKey = `${url}|${count}`;

        /*
         * Sudah ada cache.
         */
        if (this.waveformCache.has(cacheKey)) {
            this.renderWaveform(element, this.waveformCache.get(cacheKey));

            this.updateWaveformProgress();
            return;
        }

        /*
         * Jangan memasukkan request yang sama berkali-kali.
         */
        const alreadyQueued = this.waveformQueue.some(
            (item) => item.cacheKey === cacheKey,
        );

        if (alreadyQueued) {
            return;
        }

        this.waveformQueue.push({
            id,
            url,
            element,
            count,
            cacheKey,
        });

        this.processWaveformQueue();
    },

    async processWaveformQueue() {
        if (this.waveformProcessing) {
            return;
        }

        if (!this.waveformQueue.length) {
            return;
        }

        this.waveformProcessing = true;

        const item = this.waveformQueue.shift();

        try {
            const amplitudes = await this.decodeWaveform(item.url, item.count);

            this.waveformCache.set(item.cacheKey, amplitudes);

            /*
             * Element mungkin sudah dihapus oleh Livewire.
             */
            if (item.element && document.contains(item.element)) {
                this.renderWaveform(item.element, amplitudes);

                if (this.currentId === String(item.id)) {
                    this.updateWaveformProgress();
                }
            }
        } catch (error) {
            console.warn("Waveform generation failed:", error);

            if (item.element && document.contains(item.element)) {
                this.renderFallbackWaveform(item.element, item.count);
            }
        } finally {
            this.waveformProcessing = false;

            /*
             * Lanjutkan item berikutnya setelah browser
             * punya kesempatan menangani UI.
             */
            if (this.waveformQueue.length) {
                const callback =
                    window.requestIdleCallback ||
                    ((callback) => setTimeout(callback, 100));

                callback(() => {
                    this.processWaveformQueue();
                });
            }
        }
    },

    /*
    |--------------------------------------------------------------------------
    | DECODE AUDIO -> ACTUAL AMPLITUDE
    |--------------------------------------------------------------------------
    */

    async decodeWaveform(url, count) {
        /*
         * Cache sederhana berdasarkan URL + jumlah bar.
         */
        const cacheKey = `${url}|${count}`;

        if (this.waveformCache.has(cacheKey)) {
            return this.waveformCache.get(cacheKey);
        }

        const response = await fetch(url, {
            method: "GET",
            cache: "force-cache",
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch audio: ${response.status}`);
        }

        const arrayBuffer = await response.arrayBuffer();

        if (!this.audioContext) {
            const AudioContext =
                window.AudioContext || window.webkitAudioContext;

            if (!AudioContext) {
                throw new Error("Web Audio API tidak tersedia.");
            }

            this.audioContext = new AudioContext();
        }

        /*
         * Decode hanya setelah user benar-benar
         * memainkan lagu.
         */
        const audioBuffer =
            await this.audioContext.decodeAudioData(arrayBuffer);

        const channelCount = audioBuffer.numberOfChannels;

        const length = audioBuffer.length;

        if (!length || !channelCount) {
            return [];
        }

        const samplesPerBar = Math.max(1, Math.floor(length / count));

        const amplitudes = [];

        /*
         * Gunakan channel pertama.
         *
         * Untuk stereo, channel kiri sudah cukup
         * untuk bentuk waveform visual.
         */
        const data = audioBuffer.getChannelData(0);

        for (let bar = 0; bar < count; bar++) {
            const start = bar * samplesPerBar;

            const end =
                bar === count - 1
                    ? length
                    : Math.min(length, start + samplesPerBar);

            if (start >= end) {
                amplitudes.push(0);
                continue;
            }

            let sumSquares = 0;
            let peak = 0;

            /*
             * Sampling sebagian kecil saja agar
             * perhitungan waveform tidak terlalu berat.
             */
            const sampleCount = Math.min(2000, end - start);

            const step = Math.max(1, Math.floor((end - start) / sampleCount));

            let samples = 0;

            for (let i = start; i < end; i += step) {
                const value = Math.abs(data[i]);

                sumSquares += value * value;

                if (value > peak) {
                    peak = value;
                }

                samples++;
            }

            const rms = samples > 0 ? Math.sqrt(sumSquares / samples) : 0;

            /*
             * Gabungkan RMS + peak supaya bentuk
             * waveform tetap terlihat detail.
             */
            const amplitude = rms * 0.65 + peak * 0.35;

            amplitudes.push(amplitude);
        }

        return this.normalizeAmplitudes(amplitudes);
    },

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE
    |--------------------------------------------------------------------------
    */

    normalizeAmplitudes(amplitudes) {
        if (!amplitudes.length) {
            return [];
        }

        const max = Math.max(...amplitudes);

        if (max <= 0) {
            return amplitudes.map(() => 0.15);
        }

        return amplitudes.map((value) => {
            const normalized = value / max;

            // Lebih natural: bagian pelan tidak terlalu ditinggikan
            const enhanced = Math.pow(normalized, 1.2);

            // Minimum 8%, maksimum 65%
            return 0.08 + enhanced * 0.57;
        });
    },

    /*
    |--------------------------------------------------------------------------
    | RENDER WAVEFORM
    |--------------------------------------------------------------------------
    */

    renderWaveform(element, amplitudes) {
        if (!element) {
            return;
        }

        if (!amplitudes?.length) {
            this.renderFallbackWaveform(element, this.getBarCount(element));

            return;
        }

        element.innerHTML = `
            <div
                data-wave-container
                style="
                    display:flex;
                    align-items:center;
                    width:100%;
                    height:100%;
                    gap:1px;
                    overflow:hidden;
                "
            >
                ${amplitudes
                    .map(
                        (height, index) => `
                        <span
                            data-wave-bar="${index}"
                            style="
                                flex:1 1 0%;
                                min-width:0;
                                height:${height * 100}%;
                                border-radius:9999px;
                                background:#d4d4d8;
                                transition:background-color 80ms linear;
                                pointer-events:none;
                            "
                        ></span>
                    `,
                    )
                    .join("")}
            </div>
        `;

        this.updateWaveformProgress();
    },

    renderFallbackWaveform(element, count = 80) {
        if (!element) {
            return;
        }

        const bars = Array.from({ length: count }, (_, index) => {
            /*
             * Deterministic pseudo-random waveform.
             *
             * Bukan random setiap render sehingga
             * waveform tidak berubah-ubah.
             */
            const seed = Math.sin(index * 12.9898) * 43758.5453;

            const random = seed - Math.floor(seed);

            return 0.15 + random * 0.6;
        });

        this.renderWaveform(element, bars);
    },

    /*
    |--------------------------------------------------------------------------
    | WAVEFORM BAR COUNT
    |--------------------------------------------------------------------------
    */

    getBarCount(element) {
        const width = element?.clientWidth || 600;

        /*
         * Maksimal 120 bar agar DOM tetap ringan.
         */
        return Math.max(50, Math.min(120, Math.floor(width / 5)));
    },

    /*
    |--------------------------------------------------------------------------
    | WAVEFORM CLICK / SEEK
    |--------------------------------------------------------------------------
    */

    bindWaveformClicks() {
        if (this.waveformClickBound) {
            return;
        }

        this.waveformClickBound = true;

        document.addEventListener("click", (event) => {
            const waveform = event.target.closest("[data-waveform]");

            if (!waveform) {
                return;
            }

            const id = waveform.dataset.id;

            if (!id) {
                return;
            }

            /*
             * Hanya seek pada lagu yang sedang
             * aktif.
             */
            if (
                this.currentId !== id ||
                !this.audio ||
                !Number.isFinite(this.audio.duration)
            ) {
                return;
            }

            const rect = waveform.getBoundingClientRect();

            if (!rect.width) {
                return;
            }

            const ratio = (event.clientX - rect.left) / rect.width;

            const clamped = Math.max(0, Math.min(1, ratio));

            this.audio.currentTime = clamped * this.audio.duration;

            this.updateWaveformProgress();
        });
    },

    /*
    |--------------------------------------------------------------------------
    | WAVEFORM PROGRESS
    |--------------------------------------------------------------------------
    */

    updateWaveformProgress() {
        if (!this.currentId) {
            return;
        }

        if (
            !this.audio ||
            !Number.isFinite(this.audio.duration) ||
            this.audio.duration <= 0
        ) {
            return;
        }

        const progress = this.audio.currentTime / this.audio.duration;

        this.setWaveformProgress(this.currentId, progress);
    },

    setWaveformProgress(id, progress) {
        const waveform = document.querySelector(
            `[data-waveform][data-id="${CSS.escape(String(id))}"]`,
        );

        if (!waveform) {
            return;
        }

        const bars = waveform.querySelectorAll("[data-wave-bar]");

        if (!bars.length) {
            return;
        }

        const activeCount = Math.floor(bars.length * progress);

        bars.forEach((bar, index) => {
            if (index < activeCount) {
                bar.style.background = "#6366f1";
            } else {
                bar.style.background = "#d4d4d8";
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | TIME
    |--------------------------------------------------------------------------
    */

    updateDuration() {
        if (!this.currentId) {
            return;
        }

        const duration = this.audio?.duration;

        if (!Number.isFinite(duration)) {
            return;
        }

        const element = document.querySelector(
            `[data-music-duration="${CSS.escape(String(this.currentId))}"]`,
        );

        if (element) {
            element.textContent = this.formatTime(duration);
        }
    },

    updateTimeDisplay(id, currentTime, duration) {
        const current = document.querySelector(
            `[data-music-current-time="${CSS.escape(String(id))}"]`,
        );

        const total = document.querySelector(
            `[data-music-duration="${CSS.escape(String(id))}"]`,
        );

        if (current) {
            current.textContent = this.formatTime(currentTime);
        }

        if (total && Number.isFinite(duration)) {
            total.textContent = this.formatTime(duration);
        }
    },

    resetTimeDisplay(id) {
        const current = document.querySelector(
            `[data-music-current-time="${CSS.escape(String(id))}"]`,
        );

        if (current) {
            current.textContent = "00:00";
        }
    },

    formatTime(seconds) {
        if (!Number.isFinite(seconds) || seconds < 0) {
            return "00:00";
        }

        const totalSeconds = Math.floor(seconds);

        const minutes = Math.floor(totalSeconds / 60);

        const remaining = totalSeconds % 60;

        return `${String(minutes).padStart(2, "0")}:${String(
            remaining,
        ).padStart(2, "0")}`;
    },

    /*
    |--------------------------------------------------------------------------
    | ANIMATION
    |--------------------------------------------------------------------------
    */

    startAnimation() {
        this.stopAnimation();

        const animate = () => {
            if (!this.audio || this.audio.paused) {
                return;
            }

            this.updateWaveformProgress();

            this.animationFrame = requestAnimationFrame(animate);
        };

        this.animationFrame = requestAnimationFrame(animate);
    },

    stopAnimation() {
        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);

            this.animationFrame = null;
        }
    },

    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE
    |--------------------------------------------------------------------------
    */

    reinitialize() {
        /*
         * Jangan membuat Audio baru.
         */
        if (!this.audio) {
            this.init();

            return;
        }

        this.initWaveforms();

        /*
         * Kalau lagu masih aktif setelah
         * Livewire update, restore progress.
         */
        if (this.currentId) {
            this.setButtonState(this.currentId, !this.audio.paused);

            this.updateTimeDisplay(
                this.currentId,
                this.audio.currentTime || 0,
                this.audio.duration,
            );

            this.updateWaveformProgress();
        }
    },
};

/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {
    window.musicPlayer.init();
});

/*
|--------------------------------------------------------------------------
| LIVEWIRE NAVIGATION
|--------------------------------------------------------------------------
*/

document.addEventListener("livewire:navigated", () => {
    window.musicPlayer.reinitialize();
});

/*
|--------------------------------------------------------------------------
| LIVEWIRE UPDATE
|--------------------------------------------------------------------------
*/

document.addEventListener("livewire:initialized", () => {
    window.musicPlayer.init();

    Livewire.hook("morph.updated", () => {
        window.musicPlayer.reinitialize();
    });
});