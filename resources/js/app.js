/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// import {} from '@material-ui/icons';
import moment from 'moment';
import { Form, HasError, AlertError } from 'vform';
import Swal from 'sweetalert2';
import Chartkick from 'vue-chartkick';
import Chart from 'chart.js';
import VueNumber from 'vue-number-animation';
import Gate from "./Gate";

Vue.prototype.$gate = new Gate(window.user);
Vue.component('quiz',require('./components/Quiz.vue').default)
Vue.component('question',require('./components/Question.vue').default)
Vue.component('opt',require('./components/Option.vue').default)
Vue.component('assign',require('./components/Assignments.vue').default)

window.Swal = Swal;
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})
window.Toast = Toast;

Chartkick.options = {
  library: {animation: {easing: 'easeOutQuart'}},
  colors: ["#000000","#002e5a", "#007ef7","#2AD8F0"]
}

Vue.use(Chartkick.use(Chart))
Vue.use(VueNumber)

window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

let Fire = new Vue();
window.Fire = Fire;

import VueRouter from 'vue-router'

Vue.use(VueRouter)

import VueProgressBar from 'vue-progressbar'
const options = {
  color: '#bffaf3',
  failedColor: '#874b4b',
  thickness: '10px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  }
}
Vue.use(VueProgressBar, options)

let routes = [
  { path: '/home', component: require('./components/Dashboard.vue').default },
  { path: '/users', component: require('./components/Users.vue').default },
  { path: '/profile', component: require('./components/Profile.vue').default },
  { path: '/contacts', component: require('./components/Contacts.vue').default },
  { path: '/programs', component: require('./components/Programs.vue').default },
  { path: '/schools', component: require('./components/Schools.vue').default },
  { path: '/sections', component: require('./components/Sections.vue').default },
  { path: '/courses', component: require('./components/Courses.vue').default },
  { path: '/students', component: require('./components/Students.vue').default },
  { path: '/teachers', component: require('./components/Teachers.vue').default },
  { path: '/classrooms', component: require('./components/Classrooms.vue').default },
  { path: '/sessions', component: require('./components/Sessions.vue').default },
  { path: '/attendances', component: require('./components/Attendences.vue').default },
  { path: '/assignments', component: require('./components/Assignments.vue').default },
  { path: '/submissions', component: require('./components/Submissions.vue').default }
]

const router = new VueRouter({
	mode: 'history',
  	routes // short for `routes: routes`
})

Vue.filter('upText',function(value){
	if (!value) {
		return ''
	}
  	value = value.toString()
  	return value.toUpperCase()
});

Vue.filter('myDate',function(value){
	if (!value) {
		return ''
	}
  	return moment(value).format('Do MMMM YYYY')
});

Vue.component('pagination', require('laravel-vue-pagination'));
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});
