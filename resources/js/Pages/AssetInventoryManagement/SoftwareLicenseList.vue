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

defineOptions({ layout: HomeLayout });

const { formatDate, formatDateForInput } = useDateFormatter();

const props = defineProps({
    licenses: { type: Object, required: true },
});

const searchQuery = ref(
    new URLSearchParams(window.location.search).get('search') || '',
);
const currentSort = ref(
    new URLSearchParams(window.location.search).get('sort') || 'date_modified',
);

const sortOption = [
    { label: 'Software Name', value: 'name' },
    { label: 'Total Licenses', value: 'licenses' },
    { label: 'Date Modified', value: 'date_modified' },
];

const statusOptions = ['active', 'inactive', 'expired'];
const licenseTypeOptions = [
    'Per User',
    'Per Device',
    'Subscription',
    'Enterprise',
];

const closeAllModals = () => {
    document.querySelectorAll('.modal.show').forEach((element) => {
        const instance = window.bootstrap?.Modal?.getInstance(element);
        instance?.hide();
    });
};

const addForm = useForm({
    software_name: '',
    vendor: '',
    license_type: 'Per User',
    total_licenses: 1,
    used_licenses: 0,
    license_key: '',
    purchase_date: '',
    expiry_date: '',
    status: 'active',
    remarks: '',
});

const updateForm = useForm({
    id: null,
    software_name: '',
    vendor: '',
    license_type: 'Per User',
    total_licenses: 1,
    used_licenses: 0,
    license_key: '',
    purchase_date: '',
    expiry_date: '',
    status: 'active',
    remarks: '',
});

const applyFilter = (sortValue = null) => {
    if (sortValue) currentSort.value = sortValue;
    router.get(
        route('software-license.index'),
        {
            search: searchQuery.value,
            sort: currentSort.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const debouncedSearch = debounce(() => applyFilter(), 300);
watch(searchQuery, () => debouncedSearch());

const openUpdateModal = (item) => {
    updateForm.id = item.id;
    updateForm.software_name = item.software_name || '';
    updateForm.vendor = item.vendor || '';
    updateForm.license_type = item.license_type || 'Per User';
    updateForm.total_licenses = item.total_licenses ?? 1;
    updateForm.used_licenses = item.used_licenses ?? 0;
    updateForm.license_key = item.license_key || '';
    updateForm.purchase_date = formatDateForInput(item.purchase_date) || '';
    updateForm.expiry_date = formatDateForInput(item.expiry_date) || '';
    updateForm.status = item.status || 'active';
    updateForm.remarks = item.remarks || '';
};

const submitAdd = () => {
    addForm.post(route('software-license.store'), {
        onSuccess: () => {
            addForm.reset();
            addForm.license_type = 'Per User';
            addForm.status = 'active';
            addForm.total_licenses = 1;
            addForm.used_licenses = 0;
            closeAllModals();
        },
    });
};

const submitUpdate = () => {
    updateForm.put(route('software-license.update', updateForm.id), {
        onSuccess: () => closeAllModals(),
    });
};

const deleteItem = (id) => {
    Swal.fire({
        title: 'Delete software license?',
        text: 'This record will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('software-license.destroy', id));
        }
    });
};
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
                    { label: 'Software Licenses' },
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
                                Software License Inventory
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
                                    placeholder="Search software or vendor..."
                                />
                            </div>
                        </div>
                        <div
                            class="col-md-4 d-flex justify-content-md-end gap-2"
                        >
                            <button
                                class="btn btn-primary shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#AddSoftwareLicenseModal"
                            >
                                <i class="bi bi-plus-lg me-1"></i> Add License
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
                                    <th>Software</th>
                                    <th>Vendor</th>
                                    <th>License Type</th>
                                    <th>Total Licenses</th>
                                    <th>Used</th>
                                    <th>Available</th>
                                    <th>Expiry</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in props.licenses.data"
                                    :key="item.id"
                                >
                                    <td class="ps-4">{{ item.id }}</td>
                                    <td class="fw-semibold">
                                        {{ item.software_name }}
                                    </td>
                                    <td>{{ item.vendor || 'N/A' }}</td>
                                    <td>{{ item.license_type }}</td>
                                    <td>
                                        <a
                                            href="#"
                                            class="fw-bold text-decoration-none"
                                            @click.prevent="
                                                router.get(
                                                    route(
                                                        'software-license.show',
                                                        item.id,
                                                    ),
                                                )
                                            "
                                        >
                                            {{ item.total_licenses }}
                                        </a>
                                    </td>
                                    <td>{{ item.used_licenses }}</td>
                                    <td>
                                        {{
                                            item.available_licenses ??
                                            Math.max(
                                                0,
                                                item.total_licenses -
                                                    item.used_licenses,
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            item.expiry_date
                                                ? formatDate(item.expiry_date)
                                                : 'N/A'
                                        }}
                                    </td>
                                    <td class="text-center">
                                        <a
                                            class="btn btn-sm btn-outline-info me-2"
                                            :href="
                                                route(
                                                    'software-license.show',
                                                    item.id,
                                                )
                                            "
                                            @click.stop
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button
                                            class="btn btn-sm btn-outline-warning me-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#UpdateSoftwareLicenseModal"
                                            @click.stop.prevent="
                                                openUpdateModal(item)
                                            "
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click.stop.prevent="
                                                deleteItem(item.id)
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="props.licenses.data.length === 0">
                                    <td
                                        colspan="9"
                                        class="text-muted py-4 text-center"
                                    >
                                        No software licenses found.
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
                                props.licenses.from ?? 0
                            }}</span>
                            to
                            <span class="fw-bold text-dark">{{
                                props.licenses.to ?? 0
                            }}</span>
                            of
                            <span class="fw-bold text-dark">{{
                                props.licenses.total
                            }}</span>
                            entries
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !props.licenses.prev_page_url,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            props.licenses.prev_page_url &&
                                            router.get(
                                                props.licenses.prev_page_url,
                                            )
                                        "
                                        >Previous</a
                                    >
                                </li>
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !props.licenses.next_page_url,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            props.licenses.next_page_url &&
                                            router.get(
                                                props.licenses.next_page_url,
                                            )
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

    <Modals
        id="AddSoftwareLicenseModal"
        title="Add Software License"
        size="modal-lg"
    >
        <template #body>
            <form @submit.prevent="submitAdd" id="addSoftwareLicenseForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Software Name</label>
                        <input
                            v-model="addForm.software_name"
                            type="text"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <input
                            v-model="addForm.vendor"
                            type="text"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">License Type</label>
                        <select
                            v-model="addForm.license_type"
                            class="form-select"
                        >
                            <option
                                v-for="opt in licenseTypeOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Licenses</label>
                        <input
                            v-model.number="addForm.total_licenses"
                            type="number"
                            min="1"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Used Licenses</label>
                        <input
                            v-model.number="addForm.used_licenses"
                            type="number"
                            min="0"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Purchase Date</label>
                        <input
                            v-model="addForm.purchase_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input
                            v-model="addForm.expiry_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">License Key</label>
                        <textarea
                            v-model="addForm.license_key"
                            class="form-control"
                            rows="2"
                        ></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select v-model="addForm.status" class="form-select">
                            <option
                                v-for="opt in statusOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <textarea
                            v-model="addForm.remarks"
                            class="form-control"
                            rows="2"
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
                type="submit"
                form="addSoftwareLicenseForm"
                class="btn btn-primary"
                :disabled="addForm.processing"
            >
                Save
            </button>
        </template>
    </Modals>

    <Modals
        id="UpdateSoftwareLicenseModal"
        title="Update Software License"
        size="modal-lg"
    >
        <template #body>
            <form @submit.prevent="submitUpdate" id="updateSoftwareLicenseForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Software Name</label>
                        <input
                            v-model="updateForm.software_name"
                            type="text"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <input
                            v-model="updateForm.vendor"
                            type="text"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">License Type</label>
                        <select
                            v-model="updateForm.license_type"
                            class="form-select"
                        >
                            <option
                                v-for="opt in licenseTypeOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Licenses</label>
                        <input
                            v-model.number="updateForm.total_licenses"
                            type="number"
                            min="1"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Used Licenses</label>
                        <input
                            v-model.number="updateForm.used_licenses"
                            type="number"
                            min="0"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Purchase Date</label>
                        <input
                            v-model="updateForm.purchase_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input
                            v-model="updateForm.expiry_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">License Key</label>
                        <textarea
                            v-model="updateForm.license_key"
                            class="form-control"
                            rows="2"
                        ></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select v-model="updateForm.status" class="form-select">
                            <option
                                v-for="opt in statusOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <textarea
                            v-model="updateForm.remarks"
                            class="form-control"
                            rows="2"
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
                type="submit"
                form="updateSoftwareLicenseForm"
                class="btn btn-warning"
                :disabled="updateForm.processing"
            >
                Update
            </button>
        </template>
    </Modals>
</template>
