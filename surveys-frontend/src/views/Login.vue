<template>
  <div>
    <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
    <p class="mt-2 text-center text-sm text-gray-600">
      Or
      {{ ' ' }}
      <router-link :to="{name: 'Register'}" class="font-medium text-indigo-600 hover:text-indigo-500"> Register for free </router-link>
    </p>
  </div>
  <div v-if="errorMsg" class="flex items-center justify-between p-3 bg-red-400 text-white rounded">
    {{errorMsg}}
    <span class="flex justify-center items-center hover:bg-red-500 h-8 w-8 rounded-full" @click="errorMsg = ''">X</span>
  </div>
  <Form class="mt-8 space-y-6" @submit="login">
    <div class="rounded-md shadow-sm -space-y-px">
      <div>
        <label for="email" class="sr-only">Email address</label>
        <Field id="email" name="email" type="email" v-model="user.email"
        rules="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address" />
        <ErrorMessage name="email" class="text-red-400" />
      </div>
      <div>
        <label for="password" class="sr-only">Password</label>
        <Field id="password" name="password" type="password" v-model="user.password"
        rules="password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password" />
        <ErrorMessage name="password" class="text-red-400" />
      </div>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <input id="remember-me" name="remember-me" type="checkbox" v-model="user.remember"  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
        <label for="remember-me" class="ml-2 block text-sm text-gray-900"> Remember me </label>
      </div>
    </div>

    <div>
      <button
        :disabled="loading" type="submit"
        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        :class="{'cursor-not-allowed bg-indigo-500': loading}"
      >
        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
          <LockClosedIcon class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" aria-hidden="true" />
        </span>
        <LoadingIcon v-if="loading" />
        Sign in
      </button>
    </div>
  </Form>
</template>

<script setup>
  import { LockClosedIcon } from '@heroicons/vue/solid'
  import { Form, Field, ErrorMessage  } from 'vee-validate'
  import LoadingIcon from '../components/icons/LoadingIcon.vue'
  import store from '../store'
  import { useRouter } from 'vue-router'
  import {ref} from 'vue'

  const router = useRouter()

  let loading = ref(false)
  let errorMsg = ref('')
  const user = {
    email: '',
    password: '',
    remember: false
  }

  function login() {
    loading.value = true
    store.dispatch('login', user)
    .then((res) => {
      loading.value = false
      store.commit('notify', {
        type: 'success',
        message: 'You are logged in!'
      })
      router.push({ name: 'Dashboard' })
    })
    .catch((err) => {
      loading.value
      errorMsg.value = err.response.data.error
    })
  }
</script>