import './bootstrap';
import { createApp } from 'vue';
// import ExampleComponent from './components/ExampleComponent.vue';
import AccountForm from './components/AccountForm.vue';

const app = createApp({});
// app.component('example-component', ExampleComponent);
app.component('account-form-component', AccountForm);
app.mount('#app');
