<template>
   <div class="min-h-full">
      <header class="bg-white shadow">
         <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header"></slot>
         </div>
      </header>
      <main>
         <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8 bg-gray-100">
            <div class="px-4 py-6 sm:px-0">
              <slot></slot>
            </div>
         </div>
      </main>
   </div>
</template>

<script>
   export default {
      name: 'PageComponent',
   }
</script>

<style scoped>

</style>