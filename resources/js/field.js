Nova.booting((Vue, router, store) => {
  Vue.component('index-editor', require('./components/IndexField'))
  Vue.component('detail-editor', require('./components/DetailField'))
  Vue.component('form-editor', require('./components/FormField'))
})
