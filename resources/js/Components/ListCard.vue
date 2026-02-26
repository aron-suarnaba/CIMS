<setup script></setup>
<template>
  <div class="computer-card h-100 shadow-sm border-0 rounded-3 transition-all">
    <div class="card-body d-flex flex-column align-items-center p-3">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.computer-card {
  background: #fff;
  cursor: pointer;
  /* Remove fixed width/height to let Bootstrap grid handle it */
  min-height: 280px;
  display: flex;
  flex-direction: column;
}

.transition-all {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.computer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Ensure the body expands to push the badge to the bottom */
.card-body {
  height: 100%;
  justify-content: space-between;
}
</style>
