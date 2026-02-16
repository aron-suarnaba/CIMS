<script setup>
defineProps({
    id: { type: String, default: '' },
});
const navigateToDashboard = () => {
    window.location.href =
        typeof route === 'function' ? route('dashboard') : '/dashboard';
};

const videoSource = './video/WelcomeBackground.mp4';
</script>

<template>
    <section class="hero-wrapper" :id="id">
        <video autoplay muted loop playsinline class="video-bg">
            <source :src="videoSource" type="video/mp4" />
            Your browser does not support the video tag.
        </video>
        <div class="app-content">
            <div class="container-fluid px-md-5">
                <div class="row align-items-center flex-wrap-reverse pb-5">
                    <div class="col-sm-12 col-md-6 mb-3" id="leftPane">
                        <div
                            class="d-flex justify-content-center align-items-md-start align-items-center flex-column mb-3 gap-4 pt-3"
                        >
                            <h1
                                class="display-4 fw-bolder text-md-start text-center"
                                id="welcomeTextHeader"
                                data-aos="fade-right"
                                data-aos-duration="2000"
                                data-aos-easing="linear"
                            >
                                One Console. Every Asset. Absolute Control.
                            </h1>

                            <p
                                class="fs-4 text-md-start text-group mb-4 text-wrap text-center text-white"
                                data-aos="fade-right"
                                data-aos-duration="2000"
                                data-aos-easing="linear"
                            >
                                CIMS unifies your fragmented IT infrastructure
                                into a single, intelligent management layer.
                                Monitor, scale, and secure your entire ecosystem
                                from a central heartbeat.
                            </p>

                            <button
                                class="btn btn-primary btn-lg fw-bold mb-3 px-4 py-3 shadow-sm"
                                type="button"
                                @click="navigateToDashboard"
                                data-aos="fade-right"
                                data-aos-duration="1800"
                                data-aos-easing="linear"
                            >
                                Launch Dashboard
                            </button>
                        </div>
                    </div>

                    <div
                        class="col-sm-12 col-md-6 d-flex justify-content-center align-items-center mb-3"
                    ></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.hero-wrapper {
    position: relative;
    height: 100vh;
    width: 100%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    min-width: 100%;
    min-height: 100%;
    object-fit: cover;
    z-index: -1;
}

/* Darken the video so the CIMS text is readable */
.hero-wrapper::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 0;
}

.app-content {
    z-index: 1;
    color: white;
}
#welcomeTextHeader {
    font-family: 'Space Grotesk', 'Archivo', sans-serif;
    letter-spacing: -0.02em;
    line-height: 1.1;
}

/* Professional Body Font */
.text-group {
    font-family: 'Inter', 'IBM Plex Sans', sans-serif;
    max-width: 600px; /* Prevents text from stretching too wide on large screens */
}

.btn-primary {
    background-color: #2563eb; /* Matching your SVG blue */
    border: none;
    transition: transform 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    background-color: #1d4ed8;
}
</style>
