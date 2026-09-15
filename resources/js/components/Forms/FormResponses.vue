<script>
  import { Grid, html } from 'gridjs';

  export default {
    props: {
      formId: Number
    },

    components: {

    },

    data () {
      return {
        columnNames: ['internal:actions', 'internal:comment', 'internal:date'],
        columns: [
          {
            id: 'internal:actions',
            name: 'Actions',
            formatter: (_, row) => html(`<a href='mailto:${row.cells[1].data}'>Email</a>`)
          },
          {
            id: 'internal:comment',
            name: 'Comment'
          },
          {
            id: 'internal:date',
            name: 'Submission Date'
          }
        ],
        data: [],
        grid: null
      }
    },

    mounted() {
      this.load();

      this.grid = new Grid({
        columns: this.columns,
        data: this.data,
        resizable: true,
        search: true,
        style: {
          table: {
            'white-space': 'nowrap'
          }
        },
        sort: true
      }).render(document.getElementById("gridWrapper"));
    },

    methods: {
      async load() {
        $.ajax({
          url: `/api/forms/${this.formId}/responses`
        }).done(entries => {
          entries.forEach(row => {
            let response = JSON.parse(row.responseJson);
            response['internal:comment'] = row.comment;
            this.data.push(response);

            Object.keys(response).forEach(key => {
              if (this.columnNames.includes(key)) return;
              this.columnNames.push(key);
              this.columns.splice(this.columnNames.length, 0, {
                id: key,
                name: key
              });
            });
          });

          console.log(this.columns);
          console.log(this.data);

          this.grid.updateConfig({
            columns: this.columns,
            data: this.data
          }).forceRender();
        });
      },
    },
  };
</script>

<template>
  <div id="gridWrapper"></div>
</template>
