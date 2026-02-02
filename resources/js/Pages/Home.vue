<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import axios from 'axios';
import { markRaw, onMounted, ref } from 'vue';

defineOptions({ layout: HomeLayout });

// --- CHARTS CONFIGURATION ---
const chartOption = markRaw({
    chart: { id: 'cims-procurement', type: 'bar', toolbar: { show: false } },
    xaxis: { categories: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
    colors: ['#0d6efd'],
    title: { text: 'Monthly Procurement', align: 'center' },
});
const chartSeries = markRaw([
    { name: 'New Assets', data: [45, 52, 38, 24, 60, 15] },
]);

const donutChartOption = markRaw({
    chart: { id: 'cims-distribution', type: 'donut' },
    labels: ['Laptops', 'Desktops', 'Servers', 'Network', 'Monitors', 'Other'],
    colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14', '#20c997'],
    title: { text: 'Asset Composition', align: 'center' },
    legend: { position: 'bottom', horizontalAlign: 'center' },
});
const donutChartSeries = markRaw([120, 85, 15, 45, 150, 30]);

const rangeAreaChartOption = markRaw({
    chart: { id: 'cims-costs', type: 'rangeArea', toolbar: { show: false } },
    stroke: { curve: 'smooth' },
    title: { text: 'Maintenance Variance', align: 'center' },
    xaxis: { categories: ['Sep', 'Oct', 'Nov', 'Dec'] },
});
const rangeAreaChartSeries = markRaw([
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
const radialChartSeries = markRaw([88]);

// --- NEWS API LOGIC ---
const articles = ref([]);
const isNewsLoading = ref(true);

const fetchNewsFromBackend = async () => {
    try {
        const response = await axios.get('/CIMS/public/api/news/tech');
        articles.value = response.data.slice(0, 3); // Top 4 keeps the grid balanced
    } catch (error) {
        console.error('Could not load news:', error);
    } finally {
        isNewsLoading.value = false;
    }
};

onMounted(() => {
    fetchNewsFromBackend();
});
</script>

<template>
    <div class="app-content px-3 mt-4">
        <div class="container-fluid">

            <!-- This row is for the 4 main dashboard components -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
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
                    <div class="card h-100 overflow-hidden border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-3"
                        >
                            <apexchart
                                :type="chart.type"
                                width="100%"
                                height="320"
                                :options="chart.opt"
                                :series="chart.ser"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 col-lg-10">
                    <div class="bg-light h-100 rounded p-3 shadow-sm">
                        <h4 class="fw-bold">Asset Statistics</h4>
                        <p class="text-muted">
                            Main dashboard content goes here...
                        </p>
                    </div>
                </div>

                <!-- This column is for the news updates -->
                <div class="col-12 col-lg-2 mt-lg-0 mt-4">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3"
                    >
                        <h4 class="fw-bold mb-0">IT Updates</h4>
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
                                class="card h-100 news-card border-0 shadow-sm"
                            >
                                <img
                                    :src="
                                        article.urlToImage ||
                                        'https://via.placeholder.com/300x150?text=CIMS+News'
                                    "
                                    class="card-img-top"
                                    style="height: 120px; object-fit: cover"
                                />

                                <div class="card-body d-flex flex-column">
                                    <small class="text-primary fw-bold mb-1">{{
                                        article.source.name
                                    }}</small>
                                    <h6
                                        class="card-title fw-bold text-truncate-2 mb-2"
                                        style="font-size: 0.9rem"
                                    >
                                        {{ article.title }}
                                    </h6>
                                    <a
                                        :href="article.url"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary w-100 mt-auto"
                                    >
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="!isNewsLoading && articles.length === 0"
                            class="col-12 py-3 text-center"
                        >
                            <div
                                class="alert alert-info small border-0 shadow-sm"
                            >
                                No news articles found.
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
    transition: transform 0.2s ease-in-out;
}

.news-card:hover {
    transform: translateY(-5px);
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.8em;
    /* Keeps titles aligned even if one is shorter */
}
</style>
