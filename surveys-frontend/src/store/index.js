import axios from 'axios';
import { createStore } from 'vuex'
import axiosClient from '../axios'


const store = createStore({
   state: {
      user: {
         data: {},
         token: sessionStorage.getItem('Token')
      },
      dashboard: {
         loading: false,
         data: {}
      },
      currentSurvey: {
         loading: false,
         data: {}
      },
      surveys: {
         loading:false,
         links: [],
         data: []
      },
      questionTypes: ["text", "select", "radio", "checkbox", "textarea"],
      notification: {
         show: false,
         type: null,
         message: ''
      }
   },
   getters: {},
   actions: {
      async getDashBoardData({commit}) {
         commit('setDashboardLoading', true);
         await axiosClient.get('/dashboard')
                     .then((res) => {
                        commit('setDashboardLoading', false);
                        commit('setDashboardData', res.data);
                     })
                     .catch((err) => {
                        commit('setDashboardLoading', false);
                        throw err
                     })
      },
      async saveSurveyAnswer({commit}, {surveyId, answers}) {
         return await axiosClient.post(`/survey/${surveyId}/answer`, {answers});
      },
      async getSurveyBySlug({commit}, slug) {
         commit('setCurrentSurveyLoading', true)
         await axiosClient
               .get(`survey-by-slug/${slug}`)
               .then((res) => {
                  commit('setCurrentSurvey', res.data)
                  commit('setCurrentSurveyLoading', false)
                  return res
               })
               .catch((err) => {
                  commit('setCurrentSurveyLoading', false)
                  throw err
               })
      },
      async saveSurvey({commit}, survey) {
         delete survey.image_url;
         if(survey.id) {
            await axiosClient
               .put(`/survey/${survey.id}`, survey)
               .then((res) => {
                  commit('setCurrentSurvey', res.data)
                  return res;
               });
         }
         else {
            await axiosClient
               .post('/survey', survey)
               .then((res) => {
                  commit('setCurrentSurvey', res.data)
                  return res;
               })
         }
      },
      async getSurvey({commit}, id) {
         commit('setCurrentSurveyLoading', true)
         await axiosClient
                  .get(`/survey/${id}`)
                  .then((res) => {
                     commit('setCurrentSurvey', res.data);
                     commit('setCurrentSurveyLoading', false)
                     return res;
                  })
                  .catch((err) => {
                     commit('setCurrentSurveyLoading', false);
                     throw err;
                  })
      },
      async deleteSurvey({commit}, id) {
         await axiosClient
                  .delete(`/survey/${id}`)
                  .then((res) => {
                     return res;
                  })
      },
      async getSurveys({commit}, {url = null} = {}) {
         url  = url || '/survey'
         commit('setSurveysLoading', true)
         await axiosClient
                  .get(url)
                  .then((res) => {
                     commit('setSurveysLoading', false)
                     commit('setSurveys', res.data)
                  })
                  .catch((err) => {
                     commit('setSurveysLoading', false)
                     throw err;
                  })
      },
      async register({commit}, user) {
         await axiosClient
                  .post('/register', user)
                  .then(({data}) => {
                     commit('set_user', data)
                     return data
                  })
      },
      async login({commit}, user) {
         await axiosClient
                  .post('/login', user)
                  .then(({data}) => {
                     commit('set_user', data)
                     return data
                  })
      },
      async logout({commit}) {
         await axiosClient
                  .post('/logout')
                  .then(({data}) => {
                     commit('logout')
                     return data
                  })

      }
   },
   mutations: {
      setDashboardData: (state, data) => {
         state.dashboard.data = data
      },
      setDashboardLoading: (state, loading) => {
         state.dashboard.loading = loading
      },
      setSurveysLoading: (state, loading) => {
         state.surveys.loading = loading
      },
      setSurveys: (state, surveys) => {
         state.surveys.data = surveys.data
         state.surveys.links = surveys.meta.links
      },
      setCurrentSurveyLoading: (state, loading) => {
         state.currentSurvey.loading = loading;
      },
      setCurrentSurvey: (state, survey) => {
         state.currentSurvey.data = survey.data;
      },
      set_user: (state, userData) => {
         state.user.data = userData.user
         state.user.token = userData.token
         sessionStorage.setItem('Token', userData.token)
      },
      notify: (state, {message, type}) => {
         state.notification.show = true,
         state.notification.type = type,
         state.notification.message = message

         setTimeout(() => {
            state.notification.show = false;
         }, 3000)
      },
      logout: (state) => {
         state.user.data = {}
         state.user.token = null
         sessionStorage.removeItem('Token')
      }
   },
   modules: {}
})

export default store