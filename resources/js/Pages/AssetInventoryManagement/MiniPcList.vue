<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';

const { formatDateForInput } = useDateFormatter();
defineOptions({ layout: HomeLayout });

const props = defineProps({
    pcs: { type: Object, required: true },
});

// --- STATE ---
const searchQuery = ref(
    new URLSearchParams(window.location.search).get('search') || '',
);
const currentSort = ref(
    new URLSearchParams(window.location.search).get('sort') ||
        'manufacturer_model',
);

const sortOption = [
    { label: 'Manufacturer', value: 'manufacturer_model' },
    { label: 'Date Modified', value: 'date_modified' },
    { label: 'Status', value: 'status' },
];

// search debounce
const debouncedSearch = debounce(() => {
    applyFilter();
}, 300);

watch(searchQuery, () => {
    debouncedSearch();
});

const closeAllModals = () => {
    document.querySelectorAll('.modal.show').forEach((element) => {
        const instance = window.bootstrap?.Modal?.getInstance(element);
        instance?.hide();
    });
};

const getRowNumber = (index) => {
    return (props.pcs.current_page - 1) * props.pcs.per_page + (index + 1);
};

// --- FORM LOGIC ---
const addForm = useForm({
    manufacturer_model: '',
    cpu: '',
    ram: '',
    rom: '',
    mac: '',
    ip: '',
    name: '',
    purchase_date: '',
    warranty_expiry: '',
    remarks: '',
});

const updateForm = useForm({
    id: null,
    manufacturer_model: '',
    cpu: '',
    ram: '',
    rom: '',
    mac: '',
    ip: '',
    name: '',
    purchase_date: '',
    warranty_expiry: '',
    remarks: '',
});

const submitAddForm = () => {
    addForm.post(route('minipc.store'), {
        onSuccess: () => {
            addForm.reset();
            closeAllModals();
        },
        onError: (errors) => {
            const errorDetails = Object.values(errors)
                .map((err) => `<li>${err}</li>`)
                .join('');
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                html: `<ul class="text-start mt-2">${errorDetails}</ul>`,
                confirmButtonColor: '#dc3545',
            });
        },
    });
};

const submitUpdate = () => {
    updateForm.put(route('minipc.update', updateForm.id), {
        onSuccess: () => {
            closeAllModals();
        },
    });
};

const openUpdateModal = (pc) => {
    updateForm.id = pc.id;
    updateForm.manufacturer_model = pc.manufacturer_model || '';
    updateForm.cpu = pc.cpu || '';
    updateForm.ram = pc.ram || '';
    updateForm.rom = pc.rom || '';
    updateForm.mac = pc.mac || '';
    updateForm.ip = pc.ip || '';
    updateForm.name = pc.name || '';
    updateForm.purchase_date = formatDateForInput(pc.purchase_date) || '';
    updateForm.warranty_expiry = formatDateForInput(pc.warranty_expiry) || '';
    updateForm.remarks = pc.remarks || '';
};

const applyFilter = (sortValue = null) => {
    if (sortValue) {
        currentSort.value = sortValue;
    }

    router.get(
        route('minipc.index'),
        {
            search: searchQuery.value,
            sort: currentSort.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

watch(
    searchQuery,
    debounce(() => applyFilter(), 300),
);
</script>

<template>
    <div class="app-content-header py-3">
        <div class="container">
            <Breadcrumb
                :breadcrumbs="[
                    { label: 'Dashboard', url: route('dashboard') },
                    {
                        label: 'Asset & Inventory',
                        url: route('AssetAndInventoryManagement'),
                    },
                    { label: 'Mini PC' },
                ]"
            />
        </div>
    </div>

    <div class="app-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <BackButton
                    @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                    "
                />
            </div>

            <div class="card mb-5 border-0 shadow-sm">
                <div class="card-header bg-white py-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <h5 class="fw-bold text-primary mb-0">
                                List of Mini PC
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-light border-end-0"
                                >
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="form-control bg-light border-start-0"
                                    placeholder="Search manufacturer/model..."
                                    @input="debouncedSearch"
                                />
                            </div>
                        </div>

                        <div
                            class="col-md-4 d-flex justify-content-md-end gap-2"
                        >
                            <button
                                class="btn btn-primary shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#AddMiniPcModal"
                            >
                                <i class="bi bi-plus-lg me-1"></i> Add Mini PC
                            </button>

                            <div class="dropdown">
                                <button
                                    class="btn btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                >
                                    <i class="bi bi-filter-right me-1"></i> Sort
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li
                                        v-for="opt in sortOption"
                                        :key="opt.value"
                                    >
                                        <a
                                            href="#"
                                            class="dropdown-item"
                                            :class="{
                                                active:
                                                    currentSort === opt.value,
                                            }"
                                            @click.prevent="
                                                applyFilter(opt.value)
                                            "
                                        >
                                            {{ opt.label }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover mb-0 table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Manufacturer/Model</th>
                                    <th>Status</th>
                                    <th>Specs (CPU/RAM/ROM)</th>
                                    <th>Assigned To</th>
                                    <th>Date Issued</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(pc, index) in props.pcs.data"
                                    :key="pc.id"
                                    @click="
                                        router.get(
                                            route('minipc.show', { id: pc.id }),
                                        )
                                    "
                                    style="cursor: pointer"
                                >
                                    <td class="text-muted ps-4">
                                        {{ getRowNumber(index) }}
                                    </td>
                                    <td>
                                        <span
                                            class="fw-semibold text-capitalize"
                                            >{{ pc.manufacturer_model }}</span
                                        >
                                    </td>
                                    <td>
                                        <span
                                            class="badge px-3 py-2"
                                            :class="{
                                                'bg-success':
                                                    pc.status === 'available',
                                                'bg-warning text-dark':
                                                    pc.status === 'issued',
                                                'bg-danger':
                                                    pc.status === 'pullout',
                                            }"
                                        >
                                            {{
                                                pc.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                pc.status.slice(1)
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <span v-if="pc.cpu">{{
                                                pc.cpu
                                            }}</span>
                                            <span v-if="pc.cpu && pc.ram">
                                                /
                                            </span>
                                            <span v-if="pc.ram"
                                                >{{ pc.ram }} GB</span
                                            >
                                            <span
                                                v-if="
                                                    (pc.cpu || pc.ram) && pc.rom
                                                "
                                            >
                                                /
                                            </span>
                                            <span v-if="pc.rom"
                                                >{{ pc.rom }} GB</span
                                            >
                                        </small>
                                    </td>
                                    <td>
                                        <small
                                            v-if="
                                                pc.issuances && pc.issuances[0]
                                            "
                                            class="text-dark fw-semibold"
                                        >
                                            {{ pc.issuances[0].department }}
                                        </small>
                                        <small v-else class="text-muted"
                                            >—</small
                                        >
                                    </td>
                                    <td>
                                        <small
                                            v-if="
                                                pc.issuances && pc.issuances[0]
                                            "
                                            class="text-dark fw-semibold"
                                        >
                                            {{
                                                formatDate(
                                                    pc.issuances[0].date_issued,
                                                )
                                            }}
                                        </small>
                                        <small v-else class="text-muted"
                                            >—</small
                                        >
                                    </td>
                                    <td class="text-center">
                                        <a
                                            class="btn btn-sm btn-outline-info me-2"
                                            :href="route('minipc.show', pc.id)"
                                            @click.stop
                                            title="View details"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button
                                            class="btn btn-sm btn-outline-warning me-2"
                                            @click.stop.prevent="
                                                openUpdateModal(pc)
                                            "
                                            data-bs-toggle="modal"
                                            data-bs-target="#UpdateMiniPcModal"
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click.stop.prevent="
                                                router.delete(
                                                    route(
                                                        'minipc.destroy',
                                                        pc.id,
                                                    ),
                                                )
                                            "
                                            title="Delete"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer border-top-0 bg-white py-3">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-3"
                    >
                        <div class="text-secondary small">
                            Showing
                            <span class="fw-bold text-dark">{{
                                props.pcs.from ?? 0
                            }}</span>
                            to
                            <span class="fw-bold text-dark">{{
                                props.pcs.to ?? 0
                            }}</span>
                            of
                            <span class="fw-bold text-dark">{{
                                props.pcs.total
                            }}</span>
                            entries
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !props.pcs.prev_page_url,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            router.get(props.pcs.prev_page_url)
                                        "
                                        >Previous</a
                                    >
                                </li>
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !props.pcs.next_page_url,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            router.get(props.pcs.next_page_url)
                                        "
                                        >Next</a
                                    >
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Mini PC Modal -->
    <Modals id="AddMiniPcModal" title="Add Mini PC" size="modal-lg">
        <template #body>
            <form @submit.prevent="submitAddForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Manufacturer / Model</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.manufacturer_model"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CPU</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.cpu"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">RAM</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.ram"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ROM</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.rom"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">MAC</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.mac"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">IP</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.ip"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="addForm.name"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="addForm.purchase_date"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Warranty Expiry</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="addForm.warranty_expiry"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <textarea
                            class="form-control"
                            v-model="addForm.remarks"
                        ></textarea>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >
                Close
            </button>
            <button
                type="button"
                class="btn btn-primary"
                @click="submitAddForm"
            >
                Save
            </button>
        </template>
    </Modals>

    <!-- Update Mini PC Modal -->
    <Modals id="UpdateMiniPcModal" title="Edit Mini PC" size="modal-lg">
        <template #body>
            <form @submit.prevent="submitUpdate">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Manufacturer / Model</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.manufacturer_model"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CPU</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.cpu"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">RAM</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.ram"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ROM</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.rom"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">MAC</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.mac"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">IP</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.ip"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="updateForm.name"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="updateForm.purchase_date"
                        />
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Warranty Expiry</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="updateForm.warranty_expiry"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <textarea
                            class="form-control"
                            v-model="updateForm.remarks"
                        ></textarea>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >
                Close
            </button>
            <button type="button" class="btn btn-primary" @click="submitUpdate">
                Update
            </button>
        </template>
    </Modals>
</template>

<style scoped>
.table thead th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>
