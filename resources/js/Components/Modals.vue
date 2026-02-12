<script setup>
defineProps({
    id: { type: String, required: true },
    title: { type: String, default: 'Modal Title' },
    size: { type: String, default: '' },
    headerClass: { type: String, default: '' },
    headerIcon: { type: String, default: '' },
});
</script>

<template>
    <div
        class="modal fade"
        :id="id"
        tabindex="-1"
        :aria-labelledby="id + 'Label'"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" :class="size">
            <div class="modal-content">
                <div class="modal-header" :class="headerClass">
                    <i v-if="headerIcon" :class="headerIcon"></i>
                    <h1 class="modal-title fs-5" :id="id + 'Label'">
                        {{ title }}
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    <slot name="body"></slot>
                </div>

                <div class="modal-footer">
                    <slot name="footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>
