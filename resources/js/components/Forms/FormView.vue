<script>
  import { Survey, Model, StylesManager } from "survey-vue";
  import "survey-vue/modern.css";
  StylesManager.applyTheme("modern");

  export default {
    components: {
      Survey
    },

    data () {
      return {
        survey: null
      }
    },

    mounted() {
      this.loadModel();
    },

    methods: {
      async loadModel() {
        let csrf =  document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let modelJson = await fetch('/api/forms/1/model', {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-Token': csrf
          }
        });

        this.survey = new Model(await modelJson.json());
      }
    },
  };
</script>

<template>
  <Survey :survey="survey" />
</template>
