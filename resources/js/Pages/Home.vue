<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import axios from 'axios';
import { markRaw, onMounted, onUnmounted, ref } from 'vue';

defineOptions({ layout: HomeLayout });

// --- CHARTS CONFIGURATION ---
const chartOption = markRaw({
    chart: { id: 'cims-procurement', type: 'bar', toolbar: { show: false } },
    xaxis: { categories: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
    colors: ['#0d6efd'],
    title: { text: 'Monthly Procurement', align: 'center' },
});
const chartSeries = ref([
    { name: 'New Assets', data: [45, 52, 38, 24, 60, 15] },
]);

const donutChartOption = markRaw({
    chart: { id: 'cims-distribution', type: 'donut' },
    labels: ['Laptops', 'Desktops', 'Servers', 'Network', 'Monitors', 'Other'],
    colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14', '#20c997'],
    title: { text: 'Asset Composition', align: 'center' },
    legend: { position: 'bottom', horizontalAlign: 'center' },
});
const donutChartSeries = ref([120, 85, 15, 45, 150, 30]);

const rangeAreaChartOption = markRaw({
    chart: { id: 'cims-costs', type: 'rangeArea', toolbar: { show: false } },
    stroke: { curve: 'smooth' },
    title: { text: 'Maintenance Variance', align: 'center' },
    xaxis: { categories: ['Sep', 'Oct', 'Nov', 'Dec'] },
});
const rangeAreaChartSeries = ref([
    {
        name: 'Cost Range',
        data: [
            { x: 'Sep', y: [500, 1200] },
            { x: 'Oct', y: [800, 1500] },
            { x: 'Nov', y: [1200, 3000] },
            { x: 'Dec', y: [400, 900] },
        ],
    },
]);

const radialChartOption = markRaw({
    chart: { id: 'cims-compliance', type: 'radialBar' },
    plotOptions: {
        radialBar: {
            hollow: { size: '65%' },
            dataLabels: {
                name: { show: true, fontSize: '14px', offsetY: -10 },
                value: { show: true, fontSize: '20px', offsetY: 5 },
            },
        },
    },
    labels: ['Compliance'],
    colors: ['#20c997'],
});
const radialChartSeries = ref([88]);

// --- NEWS API LOGIC ---
const articles = ref([]);
const isNewsLoading = ref(true);

const fetchNewsFromBackend = async () => {
    try {
        const response = await axios.get('/CIMS/public/api/news/tech');
        articles.value = response.data.slice(0, 3);
    } catch (error) {
        console.error('Could not load news:', error);
    } finally {
        isNewsLoading.value = false;
    }
};

// --- CLOCK LOGIC ---
const time = ref('');
const date = ref('');
const week = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

const zeroPadding = (num, digit) => String(num).padStart(digit, '0');

const updateTime = () => {
    const cd = new Date();

    time.value = `${zeroPadding(cd.getHours(), 2)}:${zeroPadding(cd.getMinutes(), 2)}:${zeroPadding(cd.getSeconds(), 2)}`;

    const monthName = cd.toLocaleDateString('en-US', { month: 'long' });
    const day = zeroPadding(cd.getDate(), 2);
    const year = cd.getFullYear();
    const dayName = week[cd.getDay()];

    date.value = `${monthName} ${day}, ${year} ${dayName}`;
};
let timerID;

onMounted(() => {
    updateTime();
    timerID = setInterval(updateTime, 1000);
    fetchNewsFromBackend();
});

onUnmounted(() => {
    clearInterval(timerID);
});
</script>

<template>
    <div class="app-content mt-4 px-3">
        <div class="container-fluid">
            <!-- ROW 1 -->
            <div class="row mb-4">
                <!-- CLOCK DISPLAY -->
                <div class="col-12">
                    <div
                        class="card rounded-4 overflow-hidden border-0 bg-white shadow-sm"
                    >
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div
                                    class="col-md-6 col-lg-4 text-dark border-end-md"
                                >
                                    <p class="date-display text-muted mb-0">
                                        {{ date }}
                                    </p>
                                    <h1
                                        class="display-4 fw-bold time-display text-primary mb-0"
                                    >
                                        {{ time }}
                                    </h1>
                                    <small
                                        class="text-uppercase fw-bold opacity-50"
                                        >Local System Time</small
                                    >
                                </div>
                                <div
                                    class="col-md-6 col-lg-8 ps-md-4 mt-md-0 mt-3"
                                >
                                    <h3 class="fw-bold mb-1">CIMS Dashboard</h3>
                                    <p class="text-muted mb-0">
                                        Centralized IT Infrastructure Management
                                        System
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5">
                <div
                    class="col"
                    v-for="(chart, idx) in [
                        { type: 'bar', opt: chartOption, ser: chartSeries },
                        {
                            type: 'donut',
                            opt: donutChartOption,
                            ser: donutChartSeries,
                        },
                        {
                            type: 'rangeArea',
                            opt: rangeAreaChartOption,
                            ser: rangeAreaChartSeries,
                        },
                        {
                            type: 'radialBar',
                            opt: radialChartOption,
                            ser: radialChartSeries,
                        },
                    ]"
                    :key="idx"
                >
                    <div class="card h-100 rounded-3 border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-2"
                        >
                            <apexchart
                                :type="chart.type"
                                width="100%"
                                height="300"
                                :options="chart.opt"
                                :series="chart.ser"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-9">
                    <div class="rounded-4 h-100 bg-white p-4 shadow-sm">
                        <h4 class="fw-bold border-bottom mb-3 pb-2">
                            Asset Statistics Overview
                        </h4>
                        <div
                            class="bg-light rounded-3 border border-dashed p-5 text-center"
                        >
                            <p class="text-muted mb-0">
                                Main workspace for detailed infrastructure
                                reports.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3"
                    >
                        <h5 class="fw-bold mb-0">IT Insights</h5>
                        <div
                            v-if="isNewsLoading"
                            class="spinner-border spinner-border-sm text-primary"
                        ></div>
                    </div>

                    <div class="row g-3">
                        <div
                            v-for="article in articles"
                            :key="article.url"
                            class="col-12"
                        >
                            <div
                                class="card h-100 news-card overflow-hidden border-0 shadow-sm"
                            >
                                <img
                                    :src="
                                        article.urlToImage ||
                                        'https://via.placeholder.com/300x150?text=CIMS+News'
                                    "
                                    class="card-img-top"
                                    style="height: 120px; object-fit: cover"
                                />
                                <div class="card-body">
                                    <small
                                        class="text-primary fw-bold d-block mb-1"
                                        >{{ article.source.name }}</small
                                    >
                                    <h6
                                        class="card-title fw-bold text-truncate-2 mb-3"
                                    >
                                        {{ article.title }}
                                    </h6>
                                    <a
                                        :href="article.url"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary w-100"
                                    >
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="!isNewsLoading && articles.length === 0"
                            class="col-12"
                        >
                            <div
                                class="alert alert-light small border-0 text-center shadow-sm"
                            >
                                No recent updates found.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.news-card {
    transition: all 0.3s ease;
}
.news-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.6rem;
    font-size: 0.85rem;
    line-height: 1.3;
}
.time-display {
    font-family: 'Share Tech Mono', monospace;
    letter-spacing: -2px;
}
.date-display {
    font-family: 'Share Tech Mono', monospace;
    font-weight: bold;
    font-size: 1.1rem;
}
@media (min-width: 768px) {
    .border-end-md {
        border-right: 1px solid #dee2e6 !important;
    }
}
</style>
