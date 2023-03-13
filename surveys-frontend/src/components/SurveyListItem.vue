<template>
  <div class="flex flex-col py-4 px-6 shadow-md bg-white hover:bg-gray-50 h-[470px]">
    <img :src="survey.image_url" class="w-full h-48 object-cover" />
    <h4 class="mt-4 text-lg font-bold">{{ survey.title }}</h4>
    <div v-html="survey.description" class="overflow-hidden flex-1"></div>
    <div class="flex justify-between items-center mt-3">
      <router-link
        :to="{ name: 'SurveyView', params: { id: survey.id } }"
        class="flex items-center gap-1 py-2 px-4 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
          />
          <path
            fill-rule="evenodd"
            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
            clip-rule="evenodd"
          />
        </svg>
        Edit
      </router-link>
      <div class="flex items-center">
        <a
          :href="`/view/survey/${survey.slug}`"
          target="_blank"
          class="flex items-center py-3 px-3 text-sm text-gray-700 hover:text-white hover:bg-blue-600 rounded-full"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </a>
        <button
          v-if="survey.id"
          type="button"
          @click="emit('delete', survey.id)"
          class="flex items-center py-3 px-3 text-sm text-gray-700 hover:text-white hover:bg-red-600 rounded-full"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  const props = defineProps({
    survey: {
      type: Object,
      required: true
    }
  })

  const emit = defineEmits(['delete'])
</script>

<style lang="scss" scoped></style>
