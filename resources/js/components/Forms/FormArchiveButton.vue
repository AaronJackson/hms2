<script>
  export default {
    props: {
      formResponseId: {
        type: String
      }
    },

    methods: {
      archive(sender, options) {
        $.ajax({
          method: 'PUT',
          url: `/api/forms/responses/${this.formResponseId}/hide`,
          success: () => {
            $(sender.target).parent().parent().remove();
          }
        });
      }
    },
  };
</script>

<template>
  <button type="button" class="btn btn-primary btn-sm" @click="archive">Archive</button>
</template>
