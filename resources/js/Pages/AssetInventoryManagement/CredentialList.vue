<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import axios from 'axios';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    credentials: { type: Object, required: true },
});

const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const currentSort = ref(new URLSearchParams(window.location.search).get('sort') || 'date_modified');

const selected = ref(null);
const revealForm = ref({ password: '' });
const revealErrors = ref({});

const addForm = useForm({
    name: '',
    username: '',
    password: '',
    url: '',
    notes: '',
});

const sortOption = [
    { label: 'Name', value: 'name' },
    { label: 'Username', value: 'username' },
    { label: 'Date Modified', value: 'date_modified' },
];

const applyFilter = (sortValue = null) => {
    if (sortValue) currentSort.value = sortValue;
    router.get(
        route('credentials.index'),
        {
            search: searchQuery.value,
            sort: currentSort.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const debouncedSearch = debounce(() => applyFilter(), 300);
watch(searchQuery, () => debouncedSearch());

const deleteItem = (id) => {
    if (!confirm('Delete this credential?')) {
        return;
    }
    router.delete(route('credentials.destroy', id));
};

const fetchCredential = async (id) => {
    try {
        const res = await axios.get(route('credentials.show', id));
        selected.value = res.data.credential;
        selected.value.revealed_password = null;
        revealForm.value.password = '';
        revealErrors.value = {};
        // show bootstrap modal
        const modalEl = document.getElementById('CredentialDetailModal');
        if (modalEl && window.bootstrap) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    } catch (e) {
        console.error('Failed to load credential', e);
    }
};

const submitReveal = async () => {
    try {
        const res = await axios.post(route('credentials.reveal', selected.value.id), {
            password: revealForm.value.password,
        });
        selected.value.revealed_password = res.data.revealed_password;
        revealErrors.value = {};
    } catch (e) {
        if (e.response && e.response.data && e.response.data.errors) {
            revealErrors.value = e.response.data.errors;
        }
    }
};

const submitAdd = () => {
    addForm.post(route('credentials.store'), {
        onSuccess: () => {
            addForm.reset();
            applyFilter();
        },
    });
};
</script>

<template>
    <div class="app-content-header py-3">
        <div class="container">
            <Breadcrumb
                :breadcrumbs="[
                    { label: 'Dashboard', url: route('dashboard') },
                    { label: 'Asset & Inventory', url: route('AssetAndInventoryManagement') },
                    { label: 'Credentials' },
                ]"
            />
        </div>
    </div>

    <div class="app-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <BackButton
                    @click.prevent="router.get(route('AssetAndInventoryManagement'))"
                />
                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#AddCredentialModal"
                >
                    <i class="bi bi-plus-lg me-1"></i> Add Credential
                </button>
            </div>

            <div class="row g-3">
                <div
                    class="col-sm-6 col-md-3"
                    v-for="item in props.credentials.data"
                    :key="item.id"
                >
                    <transition name="fade">
                        <div
                            class="card h-100 shadow-sm border-0 credential-card clickable-card"
                            @click="fetchCredential(item.id)"
                        >
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>
                                    <h5 class="card-title mb-0 fw-bold text-dark text-truncate">
                                        {{ item.name }}
                                    </h5>
                                </div>

                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-globe2 me-2 small"></i>
                                        <small class="text-truncate" :title="item.url">
                                            {{ item.url || 'No URL associated' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="props.credentials.data.length === 0" class="col-12 text-center py-5">
                    No records found.
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>Showing {{ props.credentials.from || 0 }} to {{ props.credentials.to || 0 }} of {{ props.credentials.total }} entries</div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li
                                class="page-item"
                                :class="{ disabled: !props.credentials.prev_page_url }"
                            >
                                <a
                                    class="page-link"
                                    :href="props.credentials.prev_page_url"
                                >Prev</a>
                            </li>
                            <li
                                class="page-item"
                                :class="{ disabled: !props.credentials.next_page_url }"
                            >
                                <a
                                    class="page-link"
                                    :href="props.credentials.next_page_url"
                                >Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

<Modals id="AddCredentialModal" title="Create New Credential" size="modal-lg" header-class="bg-primary text-white">
    <template #body>
        <form @submit.prevent="submitAdd" id="addCredentialForm" class="p-2">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Site / App Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                        <input v-model="addForm.name" type="text" class="form-control" placeholder="e.g. Gmail, Office 365" required />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Website URL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                        <input v-model="addForm.url" type="url" class="form-control" placeholder="https://..." />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Username / Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input v-model="addForm.username" type="text" class="form-control" placeholder="user@example.com" required />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                        <input v-model="addForm.password" type="text" class="form-control font-monospace" placeholder="Choose a strong password" required />
                        </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Notes & Description</label>
                    <textarea v-model="addForm.notes" rows="3" class="form-control" placeholder="Account recovery details, hints, etc."></textarea>
                </div>
            </div>
        </form>
    </template>
    <template #footer>
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="addCredentialForm" class="btn btn-primary px-5" :disabled="addForm.processing">
            <span v-if="addForm.processing" class="spinner-border spinner-border-sm me-2"></span>
            {{ addForm.processing ? 'Saving...' : 'Create Account' }}
        </button>
    </template>
</Modals>

    <Modals id="CredentialDetailModal" title="Credential Details" size="modal-lg" header-class="bg-primary text-white">
    <template #body>
        <div v-if="selected" class="p-2">
            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                <div class="bg-primary text-white rounded-circle p-3 me-3">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ selected.name }}</h4>
                    <small class="text-muted">Account Information</small>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-uppercase small fw-bold text-muted mb-1 d-block">Username</label>
                    <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="font-monospace fs-5">{{ selected.username }}</span>
                        <button @click="copyToClipboard(selected.username)" class="btn btn-sm btn-link py-0 text-decoration-none">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-uppercase small fw-bold text-muted mb-1 d-block">Website / URL</label>
                    <div class="p-2 border-bottom">
                        <a v-if="selected.url" :href="selected.url" target="_blank" class="text-decoration-none d-block text-truncate">
                            {{ selected.url }} <i class="bi bi-box-arrow-up-right small ms-1"></i>
                        </a>
                        <span v-else class="text-muted italic">No URL provided</span>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="card border-0 bg-light p-3">
                        <label class="text-uppercase small fw-bold text-primary mb-2 d-block">Security</label>

                        <div v-if="selected.revealed_password" class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1 bg-white p-3 rounded border font-monospace fs-4 text-center letter-spacing-2">
                                {{ selected.revealed_password }}
                            </div>
                            <button @click="copyToClipboard(selected.revealed_password)" class="btn btn-outline-primary p-3">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>

                        <div v-else>
                            <form @submit.prevent="submitReveal" class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
                                <input
                                    type="password"
                                    v-model="revealForm.password"
                                    class="form-control border-start-0 py-2"
                                    placeholder="Enter Master Password to reveal"
                                />
                                <button class="btn btn-primary px-4" :disabled="!revealForm.password">
                                    <i class="bi bi-eye"></i> Reveal
                                </button>
                            </form>
                            <div class="text-danger small mt-2" v-if="revealErrors.password">
                                <i class="bi bi-exclamation-circle me-1"></i> {{ revealErrors.password[0] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="text-uppercase small fw-bold text-muted mb-1 d-block">Notes</label>
                    <div class="p-3 border rounded bg-white">
                        <p class="mb-0 text-secondary">{{ selected.notes || 'No notes provided for this account.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</Modals>
</template>

<style scoped>
.card.cursor-pointer {
    cursor: pointer;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity .3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
