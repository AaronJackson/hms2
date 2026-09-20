<script>
  export default {
    props: {
      formResponseId: {
        type: String
      },
      comment: {
        type: String
      }
    },

    methods: {
      updateComment(sender, options) {
        $.ajax({
          method: 'PUT',
          url: `/api/forms/responses/${this.formResponseId}/comment`,
          data: {
            comment: sender.target.value
          },
          success: () => {
            sender.target.classList.add('is-valid');
            setTimeout(() => {
              sender.target.classList.remove('is-valid');
            }, 1000);
          }
        });
      },
    },
  };
</script>

<template>
  <input class="form-control" :value="comment" @focusout="updateComment" />
</template>
