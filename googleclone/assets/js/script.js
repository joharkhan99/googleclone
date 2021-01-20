var timer;

$(document).ready(function () {

  $(".result").on("click", function (e) {
    var url = $(this).attr('href');       //get link of clicked site link
    var id = $(this).attr('data-linkId');  //get id of clicked site link

    if (!id) {
      alert("data-attr not found");
    }
    increaseLinkClicks(id, url);
    e.preventDefault();
  });

  // masonry package for images
  var grid = $('.imageResults');
  // load images after masonry layout is complete
  grid.on("layoutComplete", function () {
    $(".griditem img").css('visibility', 'visible');
  })
  grid.masonry({
    itemSelector: ".griditem",    //select images in grid
    columnWidth: 100,
    gutter: 5,       //spaces btw images
    transitionDuration: '0.2s',
    isInitLayout: false
  })

  // fancy box
  $('[data-fancybox]').fancybox({
    caption: function (instance, item) {
      var caption = $(this).data('caption') || '';
      var siteUrl = $(this).data('siteurl') || '';

      if (item.type === 'image') {
        caption = (caption.length ? caption + '<br />' : '') +
          '<a href="' + item.src + '">View image</a>' +
          '<br> <a href = "' + siteUrl + '" > Visit Page</a > ';
      }

      return caption;
    },
    afterShow: function (instance, item) {
      increaseImageClicks(item.src);
    }
  });

});

function loadImage(src, className) {
  var image = $("<img>");

  image.on("load", function () {
    $("." + className + " a").append(image);

    // after every hlf second call masonry for images
    clearTimeout(timer);
    timer = setTimeout(() => {
      $('.imageResults').masonry();
    }, 500);

  });

  // sent request for broken/empty images
  image.on("error", function () {
    $('.' + className).remove();
    $.ajax({
      type: "POST",
      url: "ajax/setBroken.php",
      data: {
        src: src
      }
    });
  });

  image.attr('src', src);     //insert src attr for image

}

function increaseImageClicks(url) {
  $.ajax({
    type: "POST",
    url: "ajax/updateImageCount.php",
    data: {
      url: url
    }
  });
}

function increaseLinkClicks(linkId, linkUrl) {
  $.ajax({
    type: "POST",
    url: "ajax/updateLinkCount.php",
    data: {
      id: linkId
    },
    success: function (response) {
      if (response != '') {       //means if something returns then error
        alert(response);
        return;
      }
      // else send user to clicked link
      window.location.href = linkUrl;
    }
  });
}