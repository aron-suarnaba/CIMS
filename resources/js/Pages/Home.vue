<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { reactive } from 'vue';

defineOptions({ layout: HomeLayout });

const myBreadcrumb = [{ label: 'CIMS Dashboard' }];

// 1. ASSET PROCUREMENT TREND
const chartOption = reactive({
    chart: { id: 'cims-procurement', type: 'bar', toolbar: { show: false } },
    xaxis: { categories: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
    colors: ['#0d6efd'],
    title: { text: 'Monthly Procurement', align: 'center' },
});
const chartSeries = reactive([
    { name: 'New Assets', data: [45, 52, 38, 24, 60, 15] },
]);

// 2. ASSET DISTRIBUTION
const donutChartOption = reactive({
    chart: { id: 'cims-distribution', type: 'donut' },
    labels: ['Laptops', 'Desktops', 'Servers', 'Network', 'Monitors', 'Other'],
    colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14', '#20c997'],
    title: { text: 'Asset Composition', align: 'center' },
    legend: { position: 'bottom', horizontalAlign: 'center' },
});
const donutChartSeries = reactive([120, 85, 15, 45, 150, 30]);

// 3. OPERATIONAL COST RANGE
const rangeAreaChartOption = reactive({
    chart: { id: 'cims-costs', type: 'rangeArea', toolbar: { show: false } },
    stroke: { curve: 'smooth' },
    title: { text: 'Maintenance Variance', align: 'center' },
    xaxis: { categories: ['Sep', 'Oct', 'Nov', 'Dec'] },
});
const rangeAreaChartSeries = reactive([
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

// 4. LICENSE COMPLIANCE
const radialChartOption = reactive({
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
const radialChartSeries = reactive([88]);
</script>

<template>
    <div class="app-content-header mb-4 py-3">
        <div class="container-fluid">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>

    <div class="app-content px-3">
        <div class="container-fluid">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                <div class="col">
                    <div class="card h-100 overflow-hidden border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-3"
                        >
                            <apexchart
                                type="bar"
                                width="100%"
                                height="320"
                                :options="chartOption"
                                :series="chartSeries"
                            />
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 overflow-hidden border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-3"
                        >
                            <apexchart
                                type="donut"
                                width="100%"
                                height="320"
                                :options="donutChartOption"
                                :series="donutChartSeries"
                            />
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 overflow-hidden border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-3"
                        >
                            <apexchart
                                type="rangeArea"
                                width="100%"
                                height="320"
                                :options="rangeAreaChartOption"
                                :series="rangeAreaChartSeries"
                            />
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 overflow-hidden border-0 shadow-sm">
                        <div
                            class="card-body d-flex align-items-center justify-content-center p-3"
                        >
                            <apexchart
                                type="radialBar"
                                width="100%"
                                height="320"
                                :options="radialChartOption"
                                :series="radialChartSeries"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: ensures cards don't feel too cramped on smaller screens */
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
