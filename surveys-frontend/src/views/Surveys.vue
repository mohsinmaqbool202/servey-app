<template>
   <PageComponent>
      <template v-slot:header>
         <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Surveys</h1>
            <router-link
               :to="{name: 'SurveyCreate'}"
               class="py-2 px-3 text-white bg-emerald-400 hover:bg-emerald-600 rounded-md"
            >
               + Add new Survey
            </router-link>
         </div>
      </template>
      <div v-if="surveys.loading" class="flex justify-center items-center">
         Loading...
      </div>
      <div v-else-if="!surveys.loading && surveys.data.length > 0">
         <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
         <SurveyListItem
               v-for="(survey, ind) in surveys.data"
               :key="survey.id"
               :survey="survey"
               class="opacity-0 animate-fade-in-down"
               :style="{animationDelay: `${ind * 0.1}s`}"
               @delete="deleteSurvey(survey.id)"
            />
         </div>
         <div class="flex justify-center mt-10">
            <nav
               class="relative z-0 inline-flex justify-center rounded-md shadow-sm"
               aria-label="pagination"
            >
               <a
                  v-for="(link, i) of surveys.links"
                  :key="i"
                  :disabled="!link.url"
                  href="#"
                  @click="getForPage($event, link)"
                  aria-current="page"
                  class="relative inline-flex items-center px-4 py-2 border text sm font-medium whitespace-nowrap"
                  :class="[
                     link.active
                     ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                     : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                     i===0 ? 'rounded-l-md' : '',
                     i === surveys.links.length -1 ? 'rounded-r-md' : ''
                  ]"
                  v-html="link.label"
               ></a>
            </nav>
         </div>
      </div>
      <div v-else class="text-center">
         No Data Found.
      </div>
   </PageComponent>
</template>

<script setup>
   import PageComponent from '../components/PageComponent.vue'
   import SurveyListItem from '../components/SurveyListItem.vue'
   import store from '../store'
   import { computed } from 'vue'
   import { useRouter } from 'vue-router'

   const router = useRouter();
   const surveys = computed(() => store.state.surveys)
   store.dispatch('getSurveys');

   function deleteSurvey(id) {
      if(confirm(`Are you sure you want to delete this survey? Operation can't be undone!!`)) {
         store.dispatch('deleteSurvey', id).then(() => {
            store.dispatch('getSurveys')
         })
      }
   }

   function getForPage(ev, link) {
      ev.preventDefault();
      if(!link.url || link.active) {
         return;
      }

      store.dispatch('getSurveys', { url: link.url })
   }
</script>