<template>
    <nav v-if="lastPage > 1" aria-label="Pagination navigation">
        <ul :class="containerClasses">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button
                    :class="['page-link', pageLinkClasses]"
                    @click.prevent="changePage(1)"
                    :disabled="currentPage === 1"
                >
                    <slot name="first">
                        <i class="bi bi-chevron-double-left"></i>
                    </slot>
                </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button
                    :class="['page-link', pageLinkClasses]"
                    @click.prevent="changePage(currentPage - 1)"
                    :disabled="currentPage === 1"
                >
                    <slot name="prev">Previous</slot>
                </button>
            </li>

            <li class="page-item disabled">
                <span :class="['page-link', activePageLinkClasses]">{{ currentPage }}</span>
            </li>

            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <button
                    :class="['page-link', pageLinkClasses]"
                    @click.prevent="changePage(currentPage + 1)"
                    :disabled="currentPage === lastPage"
                >
                    <slot name="next">Next</slot>
                </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <button
                    :class="['page-link', pageLinkClasses]"
                    @click.prevent="changePage(lastPage)"
                    :disabled="currentPage === lastPage"
                >
                    <slot name="last">
                        <i class="bi bi-chevron-double-right"></i>
                    </slot>
                </button>
            </li>
        </ul>
    </nav>
</template>

<script setup>
const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
    // customization
    containerClasses: {
        type: String,
        default: 'pagination pagination-sm mb-0',
    },
    pageLinkClasses: {
        type: String,
        default: '',
    },
    activePageLinkClasses: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(["update:page"]);

const changePage = (page) => {
    if (page < 1 || page > props.lastPage) return;
    emit("update:page", page);
};
</script>
