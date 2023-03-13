<template>
  <div>
    <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Register for free</h2>
    <p class="mt-2 text-center text-sm text-gray-600">
      Or
      {{ ' ' }}
      <router-link :to="{name: 'Login'}" class="font-medium text-indigo-600 hover:text-indigo-500">
        Login to your account
      </router-link>
    </p>
  </div>
  <Form class="mt-8 space-y-6" @submit="register">
    <input type="hidden" name="remember" value="true" />
    <div class="rounded-md shadow-sm -space-y-px">
      <div>
        <Field id="full-name" name="name" type="text" v-model="user.name"
          rules="name" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Your Name"
        />
        <ErrorMessage name="name" class="text-red-400" />
      </div>
      <div>
        <Field id="email-address" name="email" type="email" v-model="user.email"
          rules="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address"
        />
        <ErrorMessage name="email" class="text-red-400" />
      </div>
      <div>
        <Field id="password" name="password" type="password" v-model="user.password"
          rules="password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password"
        />
        <ErrorMessage name="password" class="text-red-400" />
      </div>
      <div>
        <Field id="password_confirmation" name="password_confirmation" type="password" v-model="user.password_confirmation" rules="confirmed:password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Confirm Password" />
      </div>
      <ErrorMessage name="password_confirmation" class="text-red-400" />
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
        Sign up
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
  import { ref } from 'vue'

  const router = useRouter()
  let loading = ref(false)

  const user = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  };

  function register() {
    loading.value = true
    store
      .dispatch('register', user)
      .then((res) => {
        loading.value = false
        router.push({
          name: 'Dashboard'
        })
      })
      .catch((err) => {
        loading.value = false
      })
  }

</script>