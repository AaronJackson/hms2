<script>
  import { Survey, Model, StylesManager } from "survey-vue";
  import "survey-vue/modern.css";
  StylesManager.applyTheme("modern");

  export default {
    props: {
      formId: {
        type: Number
      }
    },

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
        $.ajax({
          url: `/api/forms/${this.formId}/model`
        }).done(response => {
          this.survey = new Model(response);
          this.survey.onComplete.add(this.submit)
        });
      },

      async submit(sender, options) {
        $.ajax({
          url: `/api/forms/${this.formId}/response`,
          method: 'PUT',
          contentType: 'application/json',
          data: JSON.stringify(sender.data)
        }).done(response => {
          console.log(response);
        });
      }
    },
  };
</script>

<template>
  <Survey :survey="survey" />
</template>
