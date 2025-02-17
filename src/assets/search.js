// Author: Antonín Kazda (kazda01)

// searchTimeout - timeout for fast typing in search input
let searchTimeout = null;
// forcedSearch - whether search was forced by pressing enter key
let forcedSearch = false;
// lastSearch - last search query
let lastSearch = '';

// Search function - sends AJAX request to server and displays widget with results
function search(input, search_id) {
  if (input.val() != lastSearch) {
    lastSearch = input.val();
    $.ajax({
      type: "POST",
      url: `/${search_id}/search`,
      data: { search: `${input.val()}` },
      beforeSend: function () {
        $('.search-button>*').hide();
        $('.search-button').append('<div class="spinner-border spinner-border-sm" role="status"></div>');
      },
      complete: function () {
        $('.search-button .spinner-border').remove();
        $('.search-button>*').show();
      },
      success: function (response) {
        $(`#search-widget-${search_id}`).html(response);
        if ($(`#search-widget-${search_id}`).children().length > 0) {
          $(`#search-widget-${search_id}`).children().last().removeClass("border-bottom");
          $(`#search-widget-${search_id}`).find(".stretched-link.search-focus").last().attr("data-last", "true");
          $(`#search-widget-${search_id}`).find(".stretched-link.search-focus").first().attr("data-first", "true");
        }
        $(`#search-widget-${search_id}`).fadeIn();
      },
      error: function (response) {
        console.log(response);
      },
    });
  } else {
    $(`#search-widget-${search_id}`).fadeIn();
  }
}

// Try search function - checks if search should be performed
function try_search(event, input) {
  let search_id = input.data('search-id');
  if (
    input.val().length >= 4 ||
    forcedSearch
  ) {
    if (event.keyCode == 13) {
      forcedSearch = true;
      clearTimeout(searchTimeout);
      if (input.val().length > 0) {
        search(input, search_id);
      }
    }
    if (input.val().length > 0) {
      clearTimeout(searchTimeout);
      // Set new timeout
      searchTimeout = setTimeout(() => {
        search(input, search_id);
      }, 300);
    } else {
      $(`#search-widget-${search_id}`).fadeOut();
    }
  } else {
    $(`#search-widget-${search_id}`).fadeOut();
    forcedSearch = false;
  }
}

$(".search-button").on("click", function (e) {
  forcedSearch = true;
  let input = $(this).next();
  if(input.data('url').length > 0 && input.val().length >= 4) {
    window.location.href = encodeURI(input.data('url').replace('{search}', input.val()));
    return;
  }
  try_search(e, input);
  input.focus();
});

$(".search-input").on("keyup", function (e) {
  // if hit enter key, try_search will be called by .search-button 
  if (e.keyCode != 13) {
    try_search(e, $(this));
  }
});

// Scroll through results using arrows or tab
$("body").on("keydown", ".search-focus.stretched-link, .search-input", function (e) {
  if (e.keyCode == 27) {
    // esc
    $("input.search-input, .search-widget a").blur()
  }
  else if ((e.keyCode == 9 && e.shiftKey) || e.keyCode == 38) {
    // tab or arrow down
    e.preventDefault();

    if ($(e.target).data("first")) {
      $(this).closest('.search-widget').prev().focus();
    } else if ($(this).prop("tagName") == "A") {
      if ($(this).parent().prev(".search-result").length != 0) {
        $(this).parent().prev().find(".search-focus.stretched-link").first().focus();
      } else {
        $(this).closest(".container-fluid").prev().find(".search-focus.stretched-link").last().focus();
      }
    }
  } else if (e.keyCode == 9 || e.keyCode == 40) {
    // shift-tab or arrow up
    e.preventDefault();

    if ($(e.target).data("last")) {
      $(".search-focus[data-first='true']").focus();
    } else if ($(this).prop("tagName") == "INPUT") {
      $(this).next().find(".search-focus.stretched-link").first().focus();
    } else {
      if ($(this).parent().next(".search-result").length != 0) {
        $(this).parent().next().find(".search-focus.stretched-link").first().focus();
      } else {
        $(this).closest(".container-fluid").next().find(".search-focus.stretched-link").first().focus();
      }
    }
  }
});

$("body").on("focusout", "input.search-input, .search-widget a", function () {
  let old_input = $(this);
  let old_search_id = old_input.data('search-id');
  clearTimeout(searchTimeout);
  setTimeout(function () {
    if (!$(document.activeElement).hasClass("search-focus") || ($(document.activeElement).prop('tagName') == 'INPUT' && old_search_id != $(document.activeElement).data('search-id'))) {
      old_input.closest('.input-group').find('.search-widget').fadeOut();
      forcedSearch = false;
      lastSearch = '';
    }
  }, 100);
});

$("body").on("focus", ".search-widget a", function () {
  $(".search-result").removeClass("focus");
  $(this).closest(".search-result").addClass("focus");
});

$("body").on("focus", ".search-input", function () {
  $(".search-result").removeClass("focus");
});