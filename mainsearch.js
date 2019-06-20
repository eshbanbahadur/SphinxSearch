jQuery("#input").keypress(function(e) {
  if (e.which == 13) {
    jQuery(".search").click();
  }
});

function ResponsiveSearch(keycode) {
  if (keycode != 13) {
    jQuery(".search").on("click touchstart", function() {
      var res_text = social_details["result_text"];
      var res_lang = lang_name;

      jQuery(".result-wrap")
        .css("visibility", "visible")
        .css("display", "block");

				/// Detch Mobile Device //
		if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ||
		   (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.platform))) {
		 
       		 jQuery('#menu').slicknav('close');
		}	
     
      //var input =jQuery("#input").val();
      var input = jQuery("#input-1").val();
      var path_base = base_path; //use js variable

      jQuery.ajax({
        type: "POST",
        url: base_path + "sites/all/themes/nexus/templates/search.php",
        data: { input: input, path_base: path_base, lang_name: lang_name },
        async: true,
        // dataType: "JSON",
        success: AjaxSucceeded,
        error: AjaxFailed
      });

      function AjaxSucceeded(result) {
        jQuery("body").addClass("search-open");
        jQuery("#WebsiteSearch-1").empty();

        jQuery("#WebsiteSearch-1").html(result);
        var counter = jQuery("#WebsiteSearch-1 a").length;

        if (res_lang === "ar") {
          counter = counter.toString(); //convert_to_arabic_numerals(counter.toString());
        }
        jQuery("#websiteResult-1").html(res_text + " : " + counter);
      }

      function AjaxFailed(jqXHR, textStatus, errorThrown) {
        alert(jqXHR.status);
        alert(textStatus);
        alert(errorThrown);
      }
    });
  } else {
    var res_text = social_details["result_text"];
    var res_lang = lang_name;

    jQuery(".result-wrap")
      .css("visibility", "visible")
      .css("display", "block");
    //jQuery('#menu').slicknav('close');
    //var input =jQuery("#input").val();
    var input = jQuery("#input-1").val();
    var path_base = base_path; //use js variable

    jQuery.ajax({
      type: "POST",
      url: base_path + "sites/all/themes/nexus/templates/search.php",
      data: { input: input, path_base: path_base, lang_name: lang_name },
      async: true,
      // dataType: "JSON",
      success: AjaxSucceeded,
      error: AjaxFailed
    });

    function AjaxSucceeded(result) {
      //alert('in success');
      jQuery("#WebsiteSearch-1").empty();
      jQuery("body").addClass("search-open");
      jQuery("#WebsiteSearch-1").html(result);
      var counter = jQuery("#WebsiteSearch-1 a").length;

      if (res_lang === "ar") {
        counter = counter.toString(); //convert_to_arabic_numerals(counter.toString());
      }
      jQuery("#websiteResult-1").html(res_text + " : " + counter);
    }

    function AjaxFailed(jqXHR, textStatus, errorThrown) {
      alert(jqXHR.status);
      alert(textStatus);
      alert(errorThrown);
    }
  }
}
// Responsive Search Ends

jQuery(document).ready(function(jQuery) {
  var res_text = social_details["result_text"];
  var res_lang = lang_name;


  jQuery(".search").click(function() {
    jQuery("#WebsiteSearch").empty();
    jQuery("#imagessearch").empty();
    jQuery("#videos-search").empty();
    jQuery(".result-wrap").css("visibility", "visible");
    var input = jQuery("#input").val();
    var path_base = base_path; //use js variable

    jQuery.ajax({
      type: "POST",
      url: base_path + "sites/all/themes/nexus/templates/search.php",
      data: { input: input, path_base: path_base, lang_name: lang_name },
      async: true,
      // dataType: "JSON",
      success: AjaxSucceeded,
      error: AjaxFailed
    });

    function AjaxSucceeded(result) {
      jQuery("#WebsiteSearch").html(result);
      var counter = jQuery("#WebsiteSearch a").length;
      if (res_lang === "ar") {
        counter = counter.toString(); //convert_to_arabic_numerals(counter.toString());
      }
      jQuery("#websiteResult").html(res_text + " : " + counter);
    }

    function AjaxFailed(jqXHR, textStatus, errorThrown) {
      alert(jqXHR.status);
      alert(textStatus);
      alert(errorThrown);
    }

    jQuery.ajax({
      type: "POST",
      url: base_path + "sites/all/themes/nexus/templates/images-search.php",
      data: { input: input, path_base: path_base, lang_name: lang_name },
      async: true,

      error: function(jqXHR, textStatus, errorThrown) {
        alert(jqXHR.status);
        alert(textStatus);
        alert(errorThrown);
      },
      success: function(result) {
        jQuery("#imagessearch").html(result);
        var counter = jQuery(".gallery-search img").length;
        if (res_lang === "ar") {
          counter = counter.toString(); //convert_to_arabic_numerals reverted
          jQuery("#imgsearchresult").html("مجموع النتائج" + " : " + counter);
        }

        if (res_lang === "en") {
          counter = counter.toString(); //convert_to_arabic_numerals reverted
          jQuery("#imgsearchresult").html("Total Results " + " : " + counter);
        }

        ////jQuery("#imgsearchresult").html(res_text+' : ' + counter);
      }
    });

    jQuery.ajax({
      type: "POST",
      url: base_path + "sites/all/themes/nexus/templates/videos-search.php",
      data: { input: input, path_base: path_base, lang_name: lang_name },

      async: true,

      error: function(jqXHR, textStatus, errorThrown) {
        alert(jqXHR.status);
        alert(textStatus);
        alert(errorThrown);
      },
      success: function(result) {
        jQuery("#videos-search").html(result);
        var counter = jQuery("div#videos-search a").length;
        if (res_lang === "ar") {
          counter = counter.toString(); //convert_to_arabic_numerals reverted
        }
        jQuery("#VideoResult").html(res_text + " : " + counter);
      }
    });
  });
});

jQuery(".search-close").click(function() {
  var input_placeholder = social_details["input_placeholder"];
  jQuery("#input").val(" ");
  jQuery("#input").attr("placeholder", input_placeholder);
  jQuery(".result-wrap").css("visibility", "hidden");
  jQuery("#input").blur();
});

// modal box
// Get the modal
function image(e) {
  var SearchImgModal = document.getElementById("SearchImgModal");

  // Get the image and insert it inside the modal - use its "alt" text as a caption
  // var img = document.getElementById('myImg');
  var img = jQuery(e).attr("id");

  var modalImg = document.getElementById("img01");
  var captionText = document.getElementById("caption");

  SearchImgModal.style.display = "block";
  modalImg.src = jQuery(e).attr("src");
  captionText.innerHTML = jQuery(e).attr("alt");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    SearchImgModal.style.display = "none";
  };
}

jQuery(".searchclose").click(function() {
  // console.log("close func");
  document.getElementsByClassName("close")[0];
});


function convert_to_arabic_numerals(input_string) {
  var arabic_numbers = input_string
    .replace("0", "٠")
    .replace("1", "١")
    .replace("2", "٢")
    .replace("3", "٣")
    .replace("4", "٤")
    .replace("5", "٥")
    .replace("6", "٦")
    .replace("7", "٧")
    .replace("8", "٨")
    .replace("9", "٩");
  return arabic_numbers;
}

jQuery(".main-section").mousedown(function() {
  var container = jQuery(".result-wrap");

  // if the target of the click isn't the container nor a descendant of the container

  container.css("visibility", "hidden");
});
