require('./bootstrap');
// This file is for global JavaScript which is to be present on every page.
// It is compiled by Webpack, so can freely contain ES2015 code.

// Initialise Foundation plugins
$(() => {
  $(document).foundation();
  if ($('#flash-overlay-modal').lenght) {
    $('#flash-overlay-modal').foundation('open');
  }
});

$.ajaxSetup({
   headers: {
     'X-CSRF-Token': window.Laravel.csrfToken,
   }
});

$(".js-programmatic-enable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", false);
});
 
$(".js-programmatic-disable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", true);
});

$(".js-data-existing-account-ajax").select2({
  theme: "foundation",
  placeholder: "Search for a member...",
  ajax: {
    url: '/api/search/users',
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        withAccount: true,
        page: params.page
      };
    },
    processResults: function (data, params) {
      //where data._embedded.people is the array containing all my objects
      data = $.map(data, function (obj) {
              obj.id = obj.accountId;
              return obj;
      });
      
      // indicate that infinite scrolling can be used
      params.page = params.page || 1;

      return {
        results: data,
        // pagination: {
        //   more: (params.page * 30) < data.total_count
        // }
      };
    },
    cache: true
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatUser, // omitted for brevity, see the source of this page
  templateSelection: formatUserSelection // omitted for brevity, see the source of this page
});

function formatUser (user) {
  if (user.loading) return user.text;
  var markup = "<div class='select2-name'>" + user.fullname + " (" + user.username + ")" +
  "</div><div  class='select2-email'>" + user.email +
  "</div><div  class='select2-address'>" + user.address1 + ", " + user.addressPostcode +
  "</div><div  class='select2-payment-ref'>" + user.paymentRef +
  "</div>";
  return markup;
}

function formatUserSelection (user) {
  return user.fullname;
}