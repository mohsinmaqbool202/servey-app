<template>
   <div class="flex items-center justify-between">
      <h3 class="text-xl font-bold">
        {{index + 1}}. {{model.question}}
      </h3>
      <div class="flex items-center">
         <!-- Add new question -->
         <button
            type="button"
            @click="addQuestion()"
            class="flex items-center text-xs py-1 px-3 mr-2 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
         >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
               <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add
         </button>
         <!-- Delete Question -->
         <button
            type="button"
            @click="deleteQuestion()"
            class="flex items-center text-xs py-1 px-3 rounded-sm text-white bg-red-500 hover:bg-red-600"
         >
            <svg
               xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"   stroke="currentColor" stroke-width="2">
               <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete
         </button>
      </div>
   </div>
   <div class="grid gap-3 grid-cols-12">
      <!-- question -->
      <div class="mt-3 col-span-9">
         <label
            :for="'question_text_' + model.id"
            class="block text-sm font-medium text-gray-700"
         >
            Question Text
         </label>
         <input
            type="text"
            :name="'question_text_' + model.id"
            :id="'question_text_' + model.id"
            v-model="model.question"
            @input="dataChange"
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
         >
      </div>
      <!-- question type -->
      <div class="mt-3 col-span-3">
         <label
            for="question_type"
            class="block text-sm font-medium text-gray-700"
         >
            Select Question Type
         </label>
         <select
            name="question_type"
            id="question_type"
            v-model="model.type"
            @change="changeType"
            class="w-full mt-1 py-2 px-3 border-gray-300 rounded-md shadow-sm bg-white sm:text-sm"
         >
            <option v-for="type in questionTypes" :key="type" :value="type">
               {{ upperCaseFirst(type) }}
            </option>
         </select>
      </div>
   </div>
   <div class="mt-3 col-span-9">
      <label
         :for="'question_description_' + model.id"
         class="block text-sm font-medium text-gray-700"
      >
         Description
      </label>
      <textarea
         :name="'question_description_' + model.id"
         :id="'question_description_' + model.id"
         rows="3"
         v-model="model.description"
         @input="dataChange"
         class="mt-1 w-full resize-none border-gray-300 sm:text-sm rounded-md shadow-sm"
      >
      </textarea>
   </div>

   <!-- data -->
      <div v-if="shouldHaveOptions()" class="mt-2">
         <h4 class="text-sm font-semibold mb-1 flex justify-between items-center">
            Options
            <!-- add new option -->
            <button
               type="button"
               @click="addOption()"
               class="flex items-center text-xs py-1 px-2 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
            >
               <svg
                  xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
               </svg>
               Add option
            </button>
         </h4>
         <!-- <div
            v-if="!model.data.options.length"
            class="text-xs bg-gray-300 text-center py-3"
         >
            You don't have any options defined.
         </div> -->
         <!-- options list -->
         <div
            v-for="(option, index) in model.data.options"
            :key="option.uuid"
            class="flex items-center mb-1"
         >
            <span class="w-6 text-sm">{{index + 1}}</span>
            <input
               type="text"
               v-model="option.text"
               @input="dataChange"
               class="w-full rounded-sm py-1 px-2 text-xs border border-gray-300 focus:border-indigo-500"
            />
            <button
               type="button"
               class="h-6 w-6 rounded-full flex items-center justify-center border border-transparent transition-colors hover:border-red-100"
               @click="removeOption(option)"
            >
               <svg
                  xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
               </svg>
            </button>
         </div>
      </div>
   <!-- / data -->
   <hr class="my-4" />
</template>

<script setup>
   import { computed, ref } from "@vue/reactivity";
   import { v4 as uuidv4 } from 'uuid '
   import store from '../../store'

   const props = defineProps({
      question: Object,
      index: Number
   })

   const emit = defineEmits(['change', 'addQuestion', 'deleteQuestion'])

   // re-create the whole question data to avoid unintentional reference change
   const model = ref(JSON.parse(JSON.stringify(props.question)));

   // get question types from vuex
   const questionTypes = computed(() => store.state.questionTypes)

   function upperCaseFirst(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
   }

   function shouldHaveOptions() {
      return ["select", "radio", "checkbox"].includes(model.value.type);
   }

   function getOptions() {
      return model.value.data.options || [];
   }

   function setOptions(options) {
      model.value.data.options = options;
   }

   //add option
   function addOption() {
      setOptions([
         ...getOptions(),
         { uuid: uuidv4(), text: "" },
      ]);

      dataChange();
   }

   //remove option
   function removeOption(op) {
      setOptions(getOptions().filter((opt) => opt !== op));
      dataChange();
   }

   //changeType
   function changeType() {
      if(shouldHaveOptions()) {
         setOptions(getOptions() || [])
      }
      dataChange();
   }

   // emit the data change
   function dataChange() {
      const data = model.value;
      if(!shouldHaveOptions()) {
         delete data.data.options;
      }
      emit("change", data)
   }

   function addQuestion() {
      emit('addQuestion', props.index + 1)
   }

   function deleteQuestion() {
      emit('deleteQuestion', props.question)
   }

</script>

<style scoped>

</style>