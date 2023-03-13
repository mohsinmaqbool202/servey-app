<template>
   <PageComponent>
      <template v-slot:header>
         <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
               {{route.params.id ? model.title : 'Create a survey'}}
            </h1>
            <button
               v-if="route.params.id"
               @click="deleteSurvey(model.id)"
               type="button"
               class="flex items-center px-3 py-2 bg-red-400 hover:bg-red-500 rounded-md text-white">
               <svg
                  xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
               </svg>
               Delete Survey
            </button>
         </div>
      </template>
      <div v-if="surveyLoading" class="flex justify-center">Loading...</div>
      <form v-else @submit.prevent="saveSurvey" class="animate-fade-in-down">
         <div class="shadow sm:rounded-md sm:overflow-hidden">
            <!-- survey fields -->
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
               <div>
                  <label class="block text-sm font-medium text-gray-700">Image</label>
                  <div class="flex items-center mt-1">
                     <img
                        v-if="model.image_url"
                        :src="model.image_url"
                        class="w-64 h-48 object-cover"
                     >
                     <span
                        v-else
                        class="flex items-center justify-center h-12 w-12 rounded-full overflow-hidden bg-gray-100"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[80%] w-[80%] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                     </span>
                     <button
                        type="button"
                        class="relative ml-5 py-2 px-3 bg-white rounded-md border border-gray-300 text-sm shadow-sm hover:bg-gray-100"
                     >
                        <input type="file" class="absolute top-0 left-0 right-0 bottom-0 opacity-0 cursor-pointer" @change="onImageChoose">
                        Change
                     </button>
                  </div>
               </div>
               <div>
                  <label class="block text-sm font-medium text-gray-700">Title</label>
                  <input
                     type="text"
                     name="title"
                     id="title"
                     v-model="model.title"
                     class="block w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:tex-sm border-gray-300 rounded-md"
                  >
               </div>
               <div>
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                     id="description"
                     name="description"
                     v-model="model.description"
                     class="block w-full mt-1 resize-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 shadow-sm rounded-md"
                     rows="3"
                  />
               </div>
               <div>
                  <label class="block text-sm font-medium text-gray-700">Expire Date</label>
                  <input
                     type="date"
                     name="expire_date"
                     id="expire_date"
                     v-model="model.expiry_date"
                     class="block w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 shadow-sm rounded-md"
                  >
               </div>
               <div class="flex items-start">
                  <div class="flex flex-center h-5">
                     <input
                        type="checkbox"
                        id="status"
                        name="status"
                        v-model="model.status"
                        class="focus:ring-indigo-500 h-5 w-5 border-gray-300 rounded"
                     >
                  </div>
                  <div class="ml-3 text-sm">
                     <label for="status" class="block text-sm font-medium text-gray-700">Active</label>
                  </div>
               </div>
               <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                  <h3 class="text-2xl font-semibold flex  items-center justify-between">
                     Questions
                     <button
                        type="button"
                        @click="addQuestion()"
                        class="flex items-center text-sm py-1 px-4 rounded-md text-white bg-gray-600 hover:bg-gray-700"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Question
                     </button>
                  </h3>
                  <div v-if="!model.questions.length" class="text-center text-gray-600">
                     You don't have any question created
                  </div>
                  <div v-else v-for="(question, index) in model.questions" :key="index">
                     <QuestionEditor
                        :question="question"
                        :index="index"
                        @change="changeQuestion"
                        @addQuestion="addQuestion"
                        @deleteQuestion="deleteQuestion"
                     />
                  </div>
               </div>
               <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
               </div>
               <!-- submit button -->
               <div class="px-4 py-3 bg-gray-50 text-right sm:p-6">
                  <button
                     type="submit"
                     class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md text-sm text-white"
                  >
                     Submit
                  </button>
               </div>
            </div>
         </div>
      </form>
   </PageComponent>
</template>

<script setup>
   import { v4 as uuidv4 } from "uuid";
   import PageComponent from '../components/PageComponent.vue'
   import QuestionEditor from '../components/editor/QuestionEditor.vue'
   import {ref, watch, computed} from 'vue'
   import store from '../store'
   import {useRoute, useRouter} from 'vue-router'

   const route = useRoute();
   const router = useRouter();
   const surveyLoading = computed(() => store.state.currentSurvey.loading )

   //Create new survey
   let model = ref({
      title: "",
      slug: "",
      status: false,
      description: null,
      image: null,
      image_url: null,
      expire_date: null,
      questions: [],
   });

   //watch to cuurent survey data
   watch (
      () => store.state.currentSurvey.data,
      (newVal, oldVal) => {
         model.value = {
            ...JSON.parse(JSON.stringify(newVal)),
            status: !!newVal.status
         }
      }
   );

   //current survey
   if(route.params.id) {
      store.dispatch('getSurvey', route.params.id);
   }

   function onImageChoose(ev) {
      const file = ev.target.files[0];

      const reader = new FileReader();
      reader.onload = () => {
         //the file to send on backend and apply validatoin
         model.value.image = reader.result;

         //The field to display here
         model.value.image_url = reader.result;
      };
      reader.readAsDataURL(file);
   }

   function saveSurvey() {
      store.dispatch('saveSurvey', model.value)
         .then(() => {
            store.commit('notify', {
               type: 'success',
               message: 'Survey updated successfully! '
            })
            router.push({ name: "Surveys"})
         })
   }

   function deleteSurvey(id) {
      if(confirm(`Are you sure you want to delete this survey? Operation can't be undone!!`)) {
         store.dispatch('deleteSurvey', id).then(() => {
            router.push({
               name: 'Surveys'
            })
         })
      }
   }

   function changeQuestion(question) {
      model.value.questions = model.value.questions.map((q) => {
         if (q.id === question.id) {
            return question;
         }
         return q;
      });
   }

   function addQuestion(index) {

      const newQuestion = {
         id: uuidv4(),
         type: "text",
         question: "",
         description: null,
         data: {},
      };

      model.value.questions.push(newQuestion);
      // model.value.questions.splice(index, 0, newQuestion);
   }

   function deleteQuestion(question) {
      model.value.questions = model.value.questions.filter((q) => q.id !== question.id);
   }
</script>

<style scoped>

</style>