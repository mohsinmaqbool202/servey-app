<template>
   <div
      v-if="notification.show"
      class="fixed left-4 bottom-4 w-[300px] px-4 py-2 text-white animate-fade-in-down"
      :class="[notification.type === 'success' ? 'bg-emerald-500' : 'bg-red-500']"
   >
      {{notification.message}}
   </div>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore();
const notification = computed(() => store.state.notification);
</script>

<style lang="scss" scoped>

</style>