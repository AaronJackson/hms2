<script>
  import { Survey, Model, StylesManager } from "survey-vue";
  import "survey-vue/modern.css";
  StylesManager.applyTheme("modern");

  export default {
    props: {
      formId: {
        type: String
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
        options.showSaveInProgress();
        $.ajax({
          url: `/api/forms/${this.formId}/response`,
          method: 'PUT',
          contentType: 'application/json',
          data: JSON.stringify(sender.data)
        }).done(response => {
          options.showSaveSuccess();
        }).fail(response => {
          console.log(response);
          if (response.responseJSON?.errors[0]?.detail) {
            options.showSaveError(response.responseJSON.errors[0].detail);
          } else {
            options.showSaveError('An unknown error has occured.');
          }
        });
      }
    },
  };
</script>

<template>
  <Survey :survey="survey" />
</template>
