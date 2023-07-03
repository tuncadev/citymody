(function ($) {
  "use strict";

  var ajax_url = civi_template_vars.ajax_url,
    item_amount = civi_template_vars.item_amount,
    map_effects = civi_template_vars.map_effects,
    default_icon = civi_template_vars.marker_default_icon,
    map_api_key = civi_template_vars.map_api_key,
    not_jobs = civi_template_vars.not_jobs;

  var markers = [];
  var civi_map;
  var jobs_maps_filter;
  var menu_filter_wrap = $(".civi-menu-filter");
  // Only take visible one
  menu_filter_wrap.each(function () {
    if ($(this).closest(".archive-filter").is(":visible")) {
      menu_filter_wrap = $(this);
    }
  });

  var mapType = $(".maptype").data("maptype");
  if (mapType == "google_map") {
    jobs_maps_filter = $("#jobs-map-filter");
  } else if (mapType == "openstreetmap") {
    jobs_maps_filter = $("#maps");
  } else {
    jobs_maps_filter = $("#map");
  }
  var has_map = "";

  if (jobs_maps_filter.length) {
    has_map = "yes";
  }
  var ajax_call = false;
  var is_mobile = false;
  if (
    /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent
    )
  ) {
    is_mobile = true;
  }

  var civi_hover_map_effects = function () {
    if (map_effects !== "" && has_map) {
      $(".map-event .area-jobs .civi-jobs-item").each(function (i) {
        var title = $(this).find(".btn-add-to-wishlist").data("jobs-id");

        if (mapType == "google_map") {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $('div[title="marker' + title + '"]').css("z-index", "0");
              infowindow.open(null, null);
            } else if (map_effects == "shine") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        } else if (mapType == "openstreetmap") {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $(".marker-" + title)
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $(".marker-" + title)
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $(".marker-" + title).css("z-index", "0");
              $(".leaflet-popup-close-button").trigger("click");
            } else if (map_effects == "shine") {
              $(".marker-" + title)
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        } else {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $("#marker-" + title)
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $("#marker-" + title)
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $(".marker-" + title).css("z-index", "0");
              $(".mapboxgl-popup-close-button").trigger("click");
            } else if (map_effects == "shine") {
              $("#marker-" + title)
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        }
      });
    }
  };

  JOBS.elements = {
    init: function () {
      this.waypoints();
      this.jobs_layout();
      this.pagination();
      this.filter_single();
      this.filter_clear_top();
      this.filter_clear();
      this.display_clear();
      this.search_cate_location();
      this.preview_job();
      this.preview_job_tab();
      this.view_phone_number();

      var menu_filter = $(".civi-menu-filter");
      var form_top = $(".form-jobs-top-filter ");

      if ($(window).width() > 992) {
        $(".btn-canvas-filter.hidden-md-up").remove();
        $("select.hidden-md-up").remove();
      }

      if (jobs_maps_filter.length > 0) {
        JOBS.elements.ajax_load();
      }

      $(".civi-menu-filter").on("input", "input.input-control", function () {
        $(".civi-pagination").find('input[name="paged"]').val(1);
        $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });

      $(".archive-jobs select.sort-by").on("change", function () {
        $(".civi-pagination").find('input[name="paged"]').val(1);
        $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });

      function delay(callback, ms) {
        var timer = 0;
        return function () {
          var context = this,
            args = arguments;
          clearTimeout(timer);
          timer = setTimeout(function () {
            callback.apply(context, args);
          }, ms || 0);
        };
      }

      menu_filter.find('input[name="jobs_filter_salary_min"]').keyup(
        delay(function () {
          $(".civi-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          JOBS.elements.ajax_load(ajax_call);
        }, 1000)
      );

      menu_filter.find('input[name="jobs_filter_salary_max"]').keyup(
        delay(function () {
          $(".civi-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          JOBS.elements.ajax_load(ajax_call);
        }, 1000)
      );

      menu_filter
        .find('select[name="jobs_filter_rate"]')
        .on("change", function () {
          $(".civi-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          JOBS.elements.ajax_load(ajax_call);
        });

      $(".form-jobs-top-filter .btn-top-filter").on("click", function (e) {
        e.preventDefault();
        $(".civi-pagination").find('input[name="paged"]').val(1);
        $(this).data("clicked", true);
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });

      var timer;
      $(".civi-ajax-search").on("keyup input focus", "input", function () {
        var $this = $(this);
        if ($this.val()) {
          clearInterval(timer);
          timer = setTimeout(function () {
            if ($this.attr("name") == "s") {
              JOBS.elements.ajax_search($this);
            }

            if ($this.attr("name") == "jobs_location") {
              var $input = $this
                .closest(".civi-ajax-search")
                .find('input[name="s"]');
              JOBS.elements.ajax_search($input, "hide");
              JOBS.elements.ajax_search_location($this, "show");
            }
          }, 200);
        } else {
          clearInterval(timer);
        }
      });

      $(".civi-ajax-search").on("focus input", "input", function () {
        var $this = $(this);
        $(".form-field .area-result").hide();
        if ($this.val()) {
          $this.closest(".area-search").find(".focus-result").hide();
          $this.closest(".form-field").find(".area-result").show();
        } else {
          $this.closest(".form-field").find(".focus-result").show();
          $this.closest(".form-field").find(".area-result").hide();
        }
      });

      $("body").on("click", ".civi-filter-search-map .btn-close", function (e) {
        e.preventDefault();
        $("body").css("overflow", "inherit");
        $(".civi-filter-search-map").fadeOut();
        ajax_call = false;
      });

      $('.btn-hide-map input[type="checkbox"]').on("change", function () {
        var elem = $(".archive-layout .inner-content");
        var ltf = $(".layout-top-filter .nav-bar");
        if ($(this).attr("checked")) {
          $("input[value='hide_map']").prop("checked", false);
        } else {
          $("input[value='hide_map']").prop("checked", true);
        }
        if (elem.hasClass("has-map")) {
          elem.removeClass("has-map");
          elem.addClass("no-map");
          ltf.removeClass("has-map");
          ltf.addClass("no-map");
        } else {
          elem.removeClass("no-map");
          elem.addClass("has-map");
          ltf.removeClass("no-map");
          ltf.addClass("has-map");
        }
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });

      $(".toggle-select").on("click", ".toggle-show", function () {
        $(this).closest(".toggle-select").find(".toggle-list").slideToggle();
      });
    },

    search_cate_location: function () {
      var form = $(".archive-layout .civi-form-location"),
        input = form.find(".archive-search-location"),
        field_select = form.find(".civi-select2");

      $("body").on(
        "mousedown",
        ".civi-form-location .icon-arrow i",
        function (e) {
          e.preventDefault();
          var select2_container = form.find(".select2.select2-container");
          if (select2_container.hasClass("select2-container--open")) {
            field_select.select2("close");
          } else {
            field_select.select2("open");
          }
          field_select.on("select2:select", function (e) {
            var data = e.params.data;
            input.val(data.text);
          });
        }
      );

      //Geo Location
      var locationBtn = form.find(".icon-location svg");

      locationBtn.on("click", () => {
        // Check if geolocation is supported by the browser
        if ("geolocation" in navigator) {
          // Use the geolocation API to get the user's current position
          navigator.geolocation.getCurrentPosition((position) => {
            // Get the latitude and longitude from the position object
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            if (mapType == "google_map") {
              var url = "";
            } else if (mapType == "openstreetmap") {
              var url =
                "https://nominatim.openstreetmap.org/reverse?lat=" +
                latitude +
                "&lon=" +
                longitude +
                "&format=jsonv2";
            } else {
              var url =
                "https://api.mapbox.com/geocoding/v5/mapbox.places/" +
                longitude +
                "," +
                latitude +
                ".json?access_token=" +
                map_api_key +
                "";
            }

            $.ajax({
              url: url,
              type: "GET",
              success: (result) => {
                // Set the value of the location input field to the address
                input.val(result.features[0].context[2].text);
              },
              error: (error) => {
                console.log(`Error: ${error}`);
              },
            });
          });
        } else {
          // Geolocation is not supported by the browser
          console.log("Geolocation is not supported by your browser");
        }
      });
    },

    waypoints: function () {
      var $elem = $(".offset-item");

      var waypoints = $elem.waypoint(
        function (direction) {
          // Fix for different ver of waypoints plugin.
          var _self = this.element ? this.element : this;
          var $self = $(_self);
          $self.addClass("animate");
        },
        {
          offset: "85%",
          triggerOnce: true,
        }
      );
    },

    pagination: function () {
      $("body").on(
        "click",
        ".civi-pagination.ajax-call a.page-numbers",
        function (e) {
          e.preventDefault();
          $(".civi-pagination .pagination").addClass("active");
          $(".civi-pagination li .page-numbers").removeClass("current");
          $(this).addClass("current");
          var paged = $(this).text();
          var current_page = 1;
          if ($(".civi-pagination").find('input[name="paged"]').val()) {
            current_page = $(".civi-pagination")
              .find('input[name="paged"]')
              .val();
          }
          if ($(this).hasClass("next")) {
            paged = parseInt(current_page) + 1;
          }
          if ($(this).hasClass("prev")) {
            paged = parseInt(current_page) - 1;
          }
          $(".civi-pagination").find('input[name="paged"]').val(paged);
          ajax_call = true;
          if ($(this).attr("data-type") == "number") {
            JOBS.elements.scroll_to(".area-jobs");
            JOBS.elements.ajax_load(ajax_call);
          } else {
            JOBS.elements.ajax_load(ajax_call, "loadmore");
          }
        }
      );
    },

    removeClassStartingWith: function (node, begin) {
      node.removeClass(function (index, className) {
        return (
          className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []
        ).join(" ");
      });
    },

    jobs_layout: function () {
      $(".jobs-layout a").on("click", function (event) {
        event.preventDefault();
        var layout = $(this).attr("data-layout");
        var type_pagination = $(".civi-pagination").attr("data-type");
        if (type_pagination == "loadmore") {
          $(".civi-pagination").find('input[name="paged"]').val(1);
        }
        $(this).closest(".jobs-layout").find(">a").removeClass("active");
        $(this).addClass("active");
        JOBS.elements.removeClassStartingWith(
          $(".archive-layout>.inner-content"),
          "layout-"
        );
        $(this).closest(".inner-content").addClass(layout);

        $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");

        $(".area-jobs .civi-jobs-item").each(function () {
          JOBS.elements.removeClassStartingWith($(this), "layout-");
          $(this).addClass(layout);
        });

        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });
    },

    display_clear: function () {
      var archive_jobs = $(".archive-jobs");
      if ($(".civi-menu-filter ul.filter-control li.active").length > 0) {
        $(".civi-nav-filter").addClass("active");
      } else {
        $(".civi-nav-filter").removeClass("active");
      }

      if (
        $('.civi-menu-filter input[name="jobs_filter_salary_min"]').val() !==
          "" ||
        $('.civi-menu-filter input[name="jobs_filter_salary_max"]').val() !==
          "" ||
        $('.civi-menu-filter select[name="jobs_filter_rate"]').val() !== ""
      ) {
        archive_jobs.find(".civi-clear-filter").show();
      } else {
        archive_jobs.find(".civi-clear-filter").hide();
      }

      $('.civi-menu-filter input[type="checkbox"]:checked').each(function () {
        if ($(this).length > 0) {
          $(".civi-nav-filter").addClass("active");
          $(this).closest(".entry-filter").addClass("open");
          archive_jobs.find(".civi-clear-filter").show();
        } else {
          $(".civi-nav-filter").removeClass("active");
          $(this).closest(".entry-filter").removeClass("open");
          archive_jobs.find(".civi-clear-filter").hide();
        }
      });

      $(".civi-menu-filter .entry-filter").each(function () {
        if ($(this).find('input[type="checkbox"]:checked').length > 0) {
          $(this).addClass("open");
        } else {
          $(this).removeClass("open");
        }
      });
    },

    filter_clear_top: function () {
      $(".civi-clear-top-filter").on("click", function () {
        $('.form-jobs-top-filter input[name="jobs-search-location"]').val("");
        $(".form-jobs-top-filter .civi-select2").val("");
        $(".form-jobs-top-filter .civi-select2").select2("destroy");
        $(".form-jobs-top-filter .civi-select2").select2();
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });
    },

    filter_clear: function () {
      $(".civi-clear-filter").on("click", function () {
        $(".civi-menu-filter ul.filter-control li").removeClass("active");
        $('.civi-menu-filter input[type="checkbox"]').prop("checked", false);
        $('.civi-menu-filter input[name="jobs_filter_salary_min"]').val("");
        $('.civi-menu-filter input[name="jobs_filter_salary_max"]').val("");
        $('.civi-menu-filter select[name="jobs_filter_rate"]').prop(
          "selectedIndex",
          0
        );
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });
    },

    filter_single: function () {
      $(".civi-menu-filter ul.filter-control a").on("click", function (e) {
        e.preventDefault();
        $(".civi-pagination").find('input[name="paged"]').val(1);
        if ($(this).parent().hasClass("active")) {
          $(this)
            .parents(".civi-menu-filter ul.filter-control")
            .find("li")
            .removeClass("active");
          $(this).closest(".entry-filter").removeClass("open");
        } else {
          $(this)
            .parents(".civi-menu-filter ul.filter-control")
            .find("li")
            .removeClass("active");
          $(this).parent().addClass("active");
          $(this).closest(".entry-filter").addClass("open");
        }
        ajax_call = true;
        JOBS.elements.ajax_load(ajax_call);
      });
    },

    ajax_load: function (ajax_call, pagination) {
      var title,
        sort_by,
        categories,
        types,
        experience,
        career,
        skills,
        location,
        categories,
        salary_rate,
        current_term,
        type_term,
        salary_min,
        salary_max,
        location,
        jobs_layout,
        search_fields_sidebar;
      var paged = 1;
      var map_html = $(".maptype").clone();

      paged = $(".civi-pagination").find('input[name="paged"]').val();
      title = $('input[name="jobs_filter_search"]').val();
      current_term = $('input[name="current_term"]').val();
      type_term = $('input[name="type_term"]').val();
      jobs_layout = $(".jobs-layout a.active").attr("data-layout");

      search_fields_sidebar = $('input[name="search_fields_sidebar"]').val();
      var result_fields = $.parseJSON(search_fields_sidebar);

      if (result_fields.hasOwnProperty("jobs-location")) {
        location = $('input[name="jobs-location_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        location = $('input[name="jobs-search-location"]').val();
      }

      if (result_fields.hasOwnProperty("jobs-categories")) {
        categories = $('input[name="jobs-categories_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        categories = $('select[name="jobs-categories"]').val();
      }

      if (result_fields.hasOwnProperty("jobs-skills")) {
        skills = $('input[name="jobs-skills_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        skills = $('select[name="jobs-skills"]').val();
      }

      if (result_fields.hasOwnProperty("jobs-type")) {
        types = $('input[name="jobs-type_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        types = $('select[name="jobs-type"]').val();
      }

      if (result_fields.hasOwnProperty("jobs-experience")) {
        experience = $('input[name="jobs-experience_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        experience = $('select[name="jobs-experience"]').val();
      }

      if (result_fields.hasOwnProperty("jobs-career")) {
        career = $('input[name="jobs-career_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        career = $('select[name="jobs-career"]').val();
      }

      sort_by = menu_filter_wrap
        .find(".sort-by.filter-control li.active a")
        .data("sort");

      var select_sort = $('.archive-layout select[name="sort_by"]').val();
      if (select_sort) {
        sort_by = select_sort;
      }

      salary_min = menu_filter_wrap
        .find('input[name="jobs_filter_salary_min"]')
        .val();
      salary_max = menu_filter_wrap
        .find('input[name="jobs_filter_salary_max"]')
        .val();
      salary_rate = menu_filter_wrap
        .find('select[name="jobs_filter_rate"]')
        .val();

      var maptype = $(".maptype").data("maptype");

      if (maptype == "google_map") {
        var marker_cluster = null,
          googlemap_default_zoom = civi_template_vars.googlemap_default_zoom,
          not_found = civi_template_vars.not_found,
          clusterIcon = civi_template_vars.clusterIcon,
          google_map_style = civi_template_vars.google_map_style,
          google_map_type = civi_template_vars.google_map_type,
          pin_cluster_enable = civi_template_vars.pin_cluster_enable;

        var infowindow = new google.maps.InfoWindow({
          maxWidth: 370,
        });

        var silver = [
          {
            featureType: "landscape",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "transit",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "water",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "road",
            elementType: "labels.icon",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            stylers: [
              {
                hue: "#00aaff",
              },
              {
                saturation: -100,
              },
              {
                gamma: 2.15,
              },
              {
                lightness: 12,
              },
            ],
          },
          {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [
              {
                visibility: "on",
              },
              {
                lightness: 24,
              },
            ],
          },
          {
            featureType: "road",
            elementType: "geometry",
            stylers: [
              {
                lightness: 57,
              },
            ],
          },
        ];

        if (has_map) {
          var civi_search_map_option = {
            scrollwheel: true,
            scroll: { x: $(window).scrollLeft(), y: $(window).scrollTop() },
            zoom: parseInt(googlemap_default_zoom),
            mapTypeId: google_map_type,
            draggable: true,
            fullscreenControl: true,
            styles: silver,
            mapTypeControl: false,
            zoomControlOptions: {
              position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
            fullscreenControlOptions: {
              position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
          };
        }

        var civi_add_markers = function (props, map) {
          $.each(props, function (i, prop) {
            var latlng = new google.maps.LatLng(prop.lat, prop.lng),
              marker_url = prop.marker_icon,
              marker_size = new google.maps.Size(60, 60);
            var marker_icon = {
              url: marker_url,
              size: marker_size,
              scaledSize: new google.maps.Size(40, 40),
              origin: new google.maps.Point(-10, -10),
              anchor: new google.maps.Point(7, 27),
            };

            var marker = new google.maps.Marker({
              position: latlng,
              url: ".jobs-" + prop.id,
              map: map,
              jobs: prop.jobs,
              icon: marker_icon,
              draggable: false,
              title: "marker" + prop.id,
              animation: google.maps.Animation.DROP,
            });

            var prop_title = prop.data ? prop.data.post_title : prop.title;

            var contentString = document.createElement("div");
            contentString.className = "civi-marker";
            contentString.innerHTML = prop.jobs;

            var click_marker = false;

            marker.addListener("mouseover", function () {
              click_marker = true;
            });

            marker.addListener("mouseout", function () {
              click_marker = false;
            });

            google.maps.event.addListener(marker, "click", function () {
              infowindow.close();
              infowindow.setContent(contentString);
              infowindow.open(map, marker);

              var scale = Math.pow(2, map.getZoom()),
                offsety = 30 / scale || 0,
                projection = map.getProjection(),
                markerPosition = marker.getPosition(),
                markerScreenPosition =
                  projection.fromLatLngToPoint(markerPosition),
                pointHalfScreenAbove = new google.maps.Point(
                  markerScreenPosition.x,
                  markerScreenPosition.y - offsety
                ),
                aboveMarkerLatLng =
                  projection.fromPointToLatLng(pointHalfScreenAbove);
              map.panTo(aboveMarkerLatLng);

              var elem = $(marker.url);
              $(".area-jobs .civi-jobs-item").removeClass("highlight");
              if (
                elem.length > 0 &&
                click_marker &&
                $(".archive-jobs.map-event").length > 0
              ) {
                elem.addClass("highlight");
                $("html, body").animate(
                  {
                    scrollTop: elem.offset().top - 50,
                  },
                  500
                );
              }
            });

            markers.push(marker);
          });
        };

        var civi_my_location = function (map) {
          var my_location = {};
          var my_lat = "";
          var my_lng = "";

          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              function (position) {
                var pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude,
                };

                my_lat = position.coords.latitude;
                my_lng = position.coords.longitude;

                my_location = {
                  lat: parseFloat(my_lat),
                  lng: parseFloat(my_lng),
                };
              },
              function () {
                handleLocationError(true, infowindow, map.getCenter());
              }
            );
          } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infowindow, map.getCenter());
          }

          function CenterControl(controlDiv, map) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.width = "40px";
            controlUI.style.height = "40px";
            controlUI.style.margin = "10px";
            controlUI.style.textAlign = "center";
            controlUI.title = "My location";
            controlDiv.appendChild(controlUI);

            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.fontSize = "18px";
            controlText.style.lineHeight = "37px";
            controlText.style.paddingLeft = "5px";
            controlText.style.paddingRight = "5px";
            controlText.innerHTML = "<i class='fas fa-location'></i>";
            controlUI.appendChild(controlText);

            var marker_icon = {
              url: default_icon,
              scaledSize: new google.maps.Size(40, 40),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(7, 27),
            };

            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
              var current_location = new google.maps.Marker({
                position: my_location,
                map,
                icon: marker_icon,
              });

              infowindow.setPosition(my_location);
              infowindow.setContent(
                '<div class="default-result">Your location.</div>'
              );
              //infowindow.open(map);
              map.panTo(my_location);
            });
          }

          const centerControlDiv = document.createElement("div");
          CenterControl(centerControlDiv, map);

          centerControlDiv.index = 1;
          map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
            centerControlDiv
          );

          function handleLocationError(browserHasGeolocation, infowindow, pos) {
            infowindow.setPosition(pos);
            infowindow.setContent(
              browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
            );
            infowindow.open(map);
          }
        };

        if (!is_mobile) {
          civi_hover_map_effects();
        }
      } else if (maptype == "openstreetmap") {
        // Begin Openstreetmap

        var civi_osm_add_markers = function (props, maps) {
          $(".maptype").remove();
          $(map_html).insertAfter("#pac-input");

          var osm_api = $("#maps").data("key");
          var osm_level = $("#maps").data("level");
          var osm_style = $("#maps").data("style");

          var features_info = [];
          var lng_args = [];
          var lat_args = [];

          $.each(props, function (i, prop) {
            features_info.push({
              type: "Feature",
              geometry: {
                type: "Point",
                coordinates: [prop.lat, prop.lng],
              },
              properties: {
                iconSize: [40, 40],
                id: prop.id,
                icon: prop.marker_icon,
                jobs: prop.jobs,
              },
            });

            lng_args.push(prop.lng);
            lat_args.push(prop.lat);
          });

          var stores = {
            type: "FeatureCollection",
            features: features_info,
          };

          var sum_lng = 0;
          for (var i = 0; i < lng_args.length; i++) {
            sum_lng += parseInt(lng_args[i], 10);
          }

          var avg_lng = 0;

          if (sum_lng / lng_args.length) {
            avg_lng = sum_lng / lng_args.length;
          }

          var sum_lat = 0;
          for (var i = 0; i < lat_args.length; i++) {
            sum_lat += parseInt(lat_args[i], 10);
          }

          var avg_lat = 0;

          if (sum_lat / lat_args.length) {
            avg_lat = sum_lat / lat_args.length;
          }

          var container = L.DomUtil.get("maps");
          if (container != null) {
            container._leaflet_id = null;
          }

          $(".leaflet-map-pane").remove();
          $(".leaflet-control-container").remove();

          var osm_map = new L.map("maps");

          osm_map.on("load", onMapLoad);

          osm_map.setView([avg_lat, avg_lng], osm_level);

          function onMapLoad() {
            var titleLayer_id = "mapbox/" + osm_style;

            L.tileLayer(
              "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=" +
                osm_api,
              {
                attribution:
                  'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                id: titleLayer_id,
                tileSize: 512,
                zoomOffset: -1,
                accessToken: osm_api,
              }
            ).addTo(osm_map);

            /**
             * Add all the things to the page:
             * - The location listings on the side of the page
             * - The markers onto the map
             */
            addMarkers();
          }

          function flyToStore(currentFeature) {
            osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
          }

          /* This will let you use the .remove() function later on */
          if (!("remove" in Element.prototype)) {
            Element.prototype.remove = function () {
              if (this.parentNode) {
                this.parentNode.removeChild(this);
              }
            };
          }

          function addMarkers() {
            /* For each feature in the GeoJSON object above: */
            stores.features.forEach(function (marker) {
              /* Create a div element for the marker. */
              var el = document.createElement("div");
              /* Assign a unique `id` to the marker. */
              el.id = "marker-" + marker.properties.id;
              /* Assign the `marker` class to each marker for styling. */
              el.className = "marker";
              el.style.backgroundImage = "url(" + marker.properties.icon + ")";
              el.style.width = marker.properties.iconSize[0] + "px";
              el.style.height = marker.properties.iconSize[1] + "px";
              /**
               * Create a marker using the div element
               * defined above and add it to the map.
               **/
              var icon = L.divIcon({
                className: "marker-" + marker.properties.id,
                html:
                  '<div><img src="' +
                  marker.properties.icon +
                  '" alt=""></div>',
                iconSize: [48, 48],
              });

              var markers = new L.marker(
                [
                  marker.geometry.coordinates[0],
                  marker.geometry.coordinates[1],
                ],
                { icon: icon }
              );

              markers.addTo(osm_map);

              if (map_effects == "popup") {
                markers.bindPopup(marker.properties.jobs);
              } else {
                markers.bindPopup();
              }

              el.addEventListener("click", function (e) {
                /* Fly to the point */
                flyToStore(marker);
                /* Highlight listing in sidebar */
                var activeItem = document.getElementsByClassName("active");
                e.stopPropagation();
                if (activeItem[0]) {
                  activeItem[0].classList.remove("active");
                }
              });
            });
          }

          if (!is_mobile) {
            civi_hover_map_effects();
          }
        };
        // End Openstreetmap
      } else {
        // Begin Mapbox

        var civi_mapbox_add_markers = function (props, map) {
          var mapbox_api = $("#map").data("key");
          var mapbox_level = $("#map").data("level");
          var mapType = $("#map").data("type");
          mapboxgl.accessToken = mapbox_api;
          $(".mapboxgl-canary").remove();
          $(".mapboxgl-canvas-container").remove();
          $(".mapboxgl-control-container").remove();
          var features_info = [];
          var lng_args = [];
          var lat_args = [];

          $.each(props, function (i, prop) {
            features_info.push({
              type: "Feature",
              geometry: {
                type: "Point",
                coordinates: [prop.lng, prop.lat],
              },
              properties: {
                iconSize: [48, 48],
                id: prop.id,
                icon: prop.marker_icon,
                jobs: prop.jobs,
              },
            });

            lng_args.push(prop.lng);
            lat_args.push(prop.lat);
          });

          var sum_lng = 0;
          for (var i = 0; i < lng_args.length; i++) {
            sum_lng += parseInt(lng_args[i], 10);
          }

          var avg_lng = 0;

          if (sum_lng / lng_args.length) {
            avg_lng = sum_lng / lng_args.length;
          }

          var sum_lat = 0;
          for (var i = 0; i < lat_args.length; i++) {
            sum_lat += parseInt(lat_args[i], 10);
          }

          var avg_lat = 0;

          if (sum_lat / lat_args.length) {
            avg_lat = sum_lat / lat_args.length;
          }

          var map = new mapboxgl.Map({
            container: "map",
            style: "mapbox://styles/mapbox/" + mapType,
            zoom: mapbox_level,
            center: [avg_lng, avg_lat],
          });

          map.addControl(new mapboxgl.NavigationControl());

          var stores = {
            type: "FeatureCollection",
            features: features_info,
          };

          /**
           * Wait until the map loads to make changes to the map.
           */
          map.on("load", function (e) {
            /**
             * This is where your '.addLayer()' used to be, instead
             * add only the source without styling a layer
             */
            map.addLayer({
              id: "locations",
              type: "symbol",
              /* Add a GeoJSON source containing jobs coordinates and information. */
              source: {
                type: "geojson",
                data: stores,
              },
              layout: {
                "icon-image": "",
                "icon-allow-overlap": true,
              },
            });

            /**
             * Add all the things to the page:
             * - The location listings on the side of the page
             * - The markers onto the map
             */
            addMarkers();
          });

          function flyToStore(currentFeature) {
            map.flyTo({
              center: currentFeature.geometry.coordinates,
              bearing: 0,
              duration: 0,
              speed: 0.2,
              curve: 1,
              easing: function (t) {
                return t;
              },
            });
          }

          function createPopUp(currentFeature) {
            var popUps = document.getElementsByClassName("mapboxgl-popup");
            /** Check if there is already a popup on the map and if so, remove it */
            if (popUps[0]) popUps[0].remove();

            var popup = new mapboxgl.Popup({ closeOnClick: false })
              .setLngLat(currentFeature.geometry.coordinates)
              .setHTML(currentFeature.properties.jobs)
              .addTo(map);
          }

          /* This will let you use the .remove() function later on */
          if (!("remove" in Element.prototype)) {
            Element.prototype.remove = function () {
              if (this.parentNode) {
                this.parentNode.removeChild(this);
              }
            };
          }

          map.on("click", function (e) {
            /* Determine if a feature in the "locations" layer exists at that point. */
            var features = map.queryRenderedFeatures(e.point, {
              layers: ["locations"],
            });

            /* If yes, then: */
            if (features.length) {
              var clickedPoint = features[0];

              /* Fly to the point */
              flyToStore(clickedPoint);

              /* Close all other popups and display popup for clicked store */
              createPopUp(clickedPoint);
            }
          });

          function addMarkers() {
            /* For each feature in the GeoJSON object above: */
            stores.features.forEach(function (marker) {
              /* Create a div element for the marker. */
              var el = document.createElement("div");
              /* Assign a unique `id` to the marker. */
              el.id = "marker-" + marker.properties.id;
              /* Assign the `marker` class to each marker for styling. */
              el.className = "marker";
              el.style.backgroundImage = "url(" + marker.properties.icon + ")";
              el.style.width = marker.properties.iconSize[0] + "px";
              el.style.height = marker.properties.iconSize[1] + "px";
              /**
               * Create a marker using the div element
               * defined above and add it to the map.
               **/
              new mapboxgl.Marker(el, { offset: [0, -23] })
                .setLngLat(marker.geometry.coordinates)
                .addTo(map);

              el.addEventListener("click", function (e) {
                /* Fly to the point */
                flyToStore(marker);
                /* Close all other popups and display popup for clicked store */
                if (map_effects == "popup") {
                  createPopUp(marker);
                }
                /* Highlight listing in sidebar */
                var activeItem = document.getElementsByClassName("active");
                e.stopPropagation();
                if (activeItem[0]) {
                  activeItem[0].classList.remove("active");
                }
              });
            });
          }
        };

        if (!is_mobile) {
          civi_hover_map_effects();
        }

        // End Mapbox
      }

      JOBS.elements.display_clear();

      var page_item = $(".area-jobs").attr("data-item-amount");

      if (page_item) {
        item_amount = page_item;
      }

      var type_pagination = $(".civi-pagination").attr("data-type");
      $(".area-jobs .civi-jobs-item").addClass("skeleton-loading");

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "civi_jobs_archive_ajax",
          paged: paged,
          title: title,
          item_amount: item_amount,
          sort_by: sort_by,
          categories: categories,
          types: types,
          skills: skills,
          experience: experience,
          career: career,
          salary_min: salary_min,
          salary_max: salary_max,
          salary_rate: salary_rate,
          current_term: current_term,
          type_term: type_term,
          location: location,
          jobs_layout: jobs_layout,
        },
        beforeSend: function () {
          $(".civi-filter-search-map .civi-loading-effect").fadeIn();
          if ($(".form-jobs-top-filter .btn-top-filter").data("clicked")) {
            $(".btn-top-filter .btn-loading").fadeIn();
          }
        },
        success: function (data) {
          $(".btn-top-filter .btn-loading").fadeOut();
          if (maptype == "google_map") {
            if (has_map) {
              civi_map = new google.maps.Map(
                document.getElementById("jobs-map-filter"),
                civi_search_map_option
              );

              google.maps.event.trigger(civi_map, "resize");
              if (data.success === true) {
                if (data.jobs) {
                  var count_jobs = data.jobs.length;
                }
              }

              if (count_jobs == 1) {
                var boundsListener = google.maps.event.addListener(
                  civi_map,
                  "bounds_changed",
                  function (event) {
                    this.setZoom(parseInt(googlemap_default_zoom));
                    google.maps.event.removeListener(boundsListener);
                  }
                );
              }

              if (google_map_style !== "") {
                var styles = JSON.parse(google_map_style);
                civi_map.setOptions({ styles: styles });
              }

              var mapPosition = new google.maps.LatLng(
                "34.0207305",
                "-118.6919226"
              );
              civi_map.setCenter(mapPosition);
              civi_map.setZoom(parseInt(googlemap_default_zoom));
              google.maps.event.addListener(
                civi_map,
                "tilesloaded",
                function () {
                  $(".civi-filter-search-map .civi-loading-effect").fadeOut();
                }
              );
            }

            if (data.success === true) {
              if (has_map) {
                markers.forEach(function (marker) {
                  marker.setMap(null);
                });

                markers = [];
                civi_add_markers(data.jobs, civi_map);
                civi_my_location(civi_map);
                civi_map.fitBounds(
                  markers.reduce(function (bounds, marker) {
                    return bounds.extend(marker.getPosition());
                  }, new google.maps.LatLngBounds())
                );
              }

              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  $(".civi-pagination .pagination").html(data.pagination);
                  $(".archive-layout .result-count").html(data.count_post);
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }

                JOBS.elements.waypoints();
              }

              if (has_map) {
                google.maps.event.trigger(civi_map, "resize");

                if (civi_template_vars.map_pin_cluster != 0) {
                  marker_cluster = new MarkerClusterer(civi_map, markers, {
                    gridSize: 60,
                    styles: [
                      {
                        url: clusterIcon,
                        width: 66,
                        height: 65,
                        textColor: "#fff",
                      },
                    ],
                  });
                }
              }
            } else {
              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(
                    '<div class="civi-ajax-result">' + not_jobs + "</div>"
                  );
                  $(".archive-layout .result-count").html(data.count_post);
                  $(".civi-pagination .pagination").html("");
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }
              }
            }

            if (has_map) {
              civi_map.fitBounds(
                markers.reduce(function (bounds, marker) {
                  return bounds.extend(marker.getPosition());
                }, new google.maps.LatLngBounds())
              );
              google.maps.event.trigger(civi_map, "resize");
            }

            $(".area-jobs .civi-jobs-item").removeClass("skeleton-loading");
          } else if (maptype == "openstreetmap") {
            $(".civi-filter-search-map .civi-loading-effect").fadeOut();
            $(".area-jobs .civi-jobs-item").removeClass("skeleton-loading");
            if (has_map) {
              civi_osm_add_markers(data.jobs, maps);
            }
            if (data.success === true) {
              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  $(".civi-pagination .pagination").html(data.pagination);
                  $(".archive-layout .result-count").html(data.count_post);
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }

                JOBS.elements.waypoints();
              }
            } else {
              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(
                    '<div class="civi-ajax-result">' + not_jobs + "</div>"
                  );
                  $(".archive-layout .result-count").html(data.count_post);
                  $(".civi-pagination .pagination").html("");
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }
              }
            }
          } else {
            $(".civi-filter-search-map .civi-loading-effect").fadeOut();
            $(".area-jobs .civi-jobs-item").removeClass("skeleton-loading");
            if (has_map) {
              civi_mapbox_add_markers(data.jobs, map);
            }
            if (data.success === true) {
              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  $(".civi-pagination .pagination").html(data.pagination);
                  $(".archive-layout .result-count").html(data.count_post);
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  $(".filter-neighborhood").html(data.filter_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }

                JOBS.elements.waypoints();
              }
            } else {
              if (ajax_call == true) {
                if (
                  data.pagination_type == "number" ||
                  pagination !== "loadmore"
                ) {
                  $(".area-jobs").html(
                    '<div class="civi-ajax-result">' + not_jobs + "</div>"
                  );
                  $(".archive-layout .result-count").html(data.count_post);
                  $(".civi-pagination .pagination").html("");
                } else {
                  $(".area-jobs").append(data.jobs_html);
                  if (data.hidden_pagination) {
                    $(".civi-pagination .pagination").html("");
                  }
                  $(".civi-pagination .pagination").removeClass("active");
                }
              }
            }
          }

          if (!is_mobile) {
            civi_hover_map_effects();
          }
          JOBS.elements.preview_job();
        },
      });
    },

    preview_job: function () {
      $(".inner-content.layout-full .civi-jobs-item").each(function () {
        var _this = $(this);
        var wrapper = $(".col-right.preview-job-wrapper");
        if ($(window).width() > 991) {
          _this.on("click", function (e) {
            e.preventDefault();
            $(".inner-content.layout-full .civi-jobs-item").removeClass(
              "active"
            );
            _this.addClass("active");
            var id = $(this).attr("data-jobid");
            $.ajax({
              url: ajax_url,
              type: "POST",
              cache: false,
              dataType: "json",
              data: {
                id: id,
                layout: "layout-full",
                action: "preview_job",
              },
              beforeSend: function () {
                wrapper
                  .find(".block-jobs-warrper")
                  .addClass("skeleton-loading");
              },
              success: function (data) {
                wrapper.text("");
                wrapper
                  .find(".block-jobs-warrper")
                  .removeClass("skeleton-loading");
                wrapper.append(data.content);
                var top = $(".inner-content.layout-full .col-right").offset()
                  .top;
                $("html, body").animate({ scrollTop: top }, "slow");
                $(".toggle-social").on("click", ".btn-share", function (e) {
                  e.preventDefault();
                  $(this).parent().toggleClass("active");
                  $(this).parent().find(".social-share").slideToggle(300);
                });
                var $form_popup = $(".form-popup-apply");
                var $btn_close = $form_popup.find(".btn-close");
                var $bg_overlay = $form_popup.find(".bg-overlay");
                var $btn_cancel = $form_popup.find(".button-cancel");

                $form_popup.each(function () {
                  var $form_popup_id = $("#" + $(this).attr("id"));
                  var $btn_popup = $(
                    ".civi-button-apply." + $(this).attr("id")
                  );
                  function open_popup(e) {
                    e.preventDefault();
                    $form_popup_id.css({ opacity: "1", visibility: "unset" });
                  }

                  function close_popup(e) {
                    e.preventDefault();
                    $form_popup_id.css({ opacity: "0", visibility: "hidden" });
                  }
                  $btn_popup.on("click", open_popup);
                  $bg_overlay.on("click", close_popup);
                  $btn_close.on("click", close_popup);
                  $btn_cancel.on("click", close_popup);
                });

                var ajax_url = civi_template_vars.ajax_url,
                  apply_saved = civi_template_vars.apply_saved,
                  not_file = civi_template_vars.not_file,
                  $form_popup = $(".form-popup-apply"),
                  title = civi_upload_cv_vars.title,
                  cv_file = civi_upload_cv_vars.cv_file,
                  cv_max_file_size = civi_upload_cv_vars.cv_max_file_size,
                  text = civi_upload_cv_vars.text,
                  url = civi_upload_cv_vars.url,
                  upload_nonce = civi_upload_cv_vars.upload_nonce;

                $form_popup.each(function () {
                  var $btn_submit = $(
                    "#" + $(".btn-submit-apply-jobs").attr("id")
                  );
                  var $btn_popup = $(
                    ".civi-button-apply." + $(this).attr("id")
                  );
                  var apply_form = $("#" + $(this).attr("id"));
                  $btn_submit.on("click", function (e) {
                    e.preventDefault();
                    var $this = $(this),
                      emaill = apply_form
                        .find('input[name="apply_emaill"]')
                        .val(),
                      message = apply_form
                        .find('textarea[name="apply_message"]')
                        .val(),
                      phone = apply_form
                        .find('input[name="apply_phone"]')
                        .val(),
                      candidate_id = $btn_popup.data("candidate_id"),
                      jobs_id = $btn_popup.data("jobs_id"),
                      cv_url = apply_form
                        .find('input[name="jobs_cv_url"]')
                        .val(),
                      type_apply = apply_form
                        .find('input[name="type_apply"]')
                        .val();

                    $.ajax({
                      type: "POST",
                      url: ajax_url,
                      dataType: "json",
                      data: {
                        action: "jobs_add_to_apply",
                        jobs_id: jobs_id,
                        candidate_id: candidate_id,
                        emaill: emaill,
                        phone: phone,
                        message: message,
                        cv_url: cv_url,
                        type_apply: type_apply,
                      },
                      beforeSend: function () {
                        $this.find(".btn-loading").fadeIn();
                      },
                      success: function (data) {
                        if (data.success == true) {
                          apply_form.find(".message_error").addClass("true");
                          apply_form.find(".message_error").text(data.message);
                          $(
                            ".civi-button-apply[data-jobs_id =" + jobs_id + "]"
                          ).html(apply_saved);
                          location.reload();
                        } else {
                          $(".message_error").text(data.message);
                        }
                        $this.find(".btn-loading").fadeOut();
                      },
                    });
                  });
                });

                var featured_image = function () {
                  var uploader_featured_image = new plupload.Uploader({
                    browse_button: "civi_select_cv",
                    file_data_name: "civi_thumbnail_upload_file",
                    drop_element: "civi_select_cv",
                    container: "civi_cv_plupload_container",
                    url: url,
                    filters: {
                      mime_types: [
                        {
                          title: title,
                          extensions: cv_file,
                        },
                      ],
                      max_file_size: cv_max_file_size,
                      prevent_duplicates: true,
                    },
                  });
                  uploader_featured_image.init();

                  uploader_featured_image.bind(
                    "UploadProgress",
                    function (up, file) {
                      $("#civi_select_cv i").removeClass(
                        "far fa-arrow-from-bottom large"
                      );
                      $("#civi_select_cv i").addClass(
                        "fal fa-spinner fa-spin large"
                      );
                    }
                  );

                  uploader_featured_image.bind(
                    "FilesAdded",
                    function (up, files) {
                      var maxfiles = 1;
                      up.refresh();
                      uploader_featured_image.start();
                    }
                  );
                  uploader_featured_image.bind("Error", function (up, err) {
                    document.getElementById("cv_errors_log").innerHTML +=
                      "Error #" + err.code + ": " + err.message + "<br/>";
                  });

                  uploader_featured_image.bind(
                    "FileUploaded",
                    function (up, file, ajax_response) {
                      var response = $.parseJSON(ajax_response.response);
                      if (response.success) {
                        $(".cv_url").val(response.url);
                        $("#civi_drop_cv").attr(
                          "data-attachment-id",
                          response.attachment_id
                        );
                        $("#civi_drop_cv").append(
                          '<a class="icon cv-icon-delete" data-jobs-id="0"  data-attachment-id="' +
                            response.attachment_id +
                            '" href="#" ><i class="far fa-trash-alt large"></i></a>'
                        );
                        var $html =
                          '<i class="far fa-arrow-from-bottom large"></i><span>' +
                          response.title +
                          "</span>";
                        $("#civi_select_cv i").addClass(
                          "far fa-arrow-from-bottom large"
                        );
                        $("#civi_select_cv").html($html);
                        $("#cv_url-error").hide();
                        $("#candidate-profile-form")
                          .find(".point-mark")
                          .change();
                      }
                    }
                  );
                };
                featured_image();
                var civi_jobs_thumb_event = function ($type) {
                  $("body").on("click", ".cv-icon-delete", function (e) {
                    e.preventDefault();
                    var $this = $(this),
                      icon_delete = $this,
                      thumbnail = $this.closest(".media-thumb-wrap"),
                      jobs_id = $this.data("jobs-id"),
                      attachment_id = $this.data("attachment-id");
                    icon_delete.html(
                      '<i class="fal fa-spinner fa-spin large"></i>'
                    );

                    $.ajax({
                      type: "post",
                      url: ajax_url,
                      dataType: "json",
                      data: {
                        action: "civi_thumbnail_remove_ajax",
                        jobs_id: jobs_id,
                        attachment_id: attachment_id,
                        type: $type,
                        removeNonce: upload_nonce,
                      },
                      beforeSend: function () {
                        icon_delete.html(
                          '<i class="fal fa-spinner fa-spin large"></i>'
                        );
                      },
                      success: function (response) {
                        if (response.success) {
                          $("#cv_url-error").show();
                          $(".civi_cv_file").show();
                        }
                        $("#civi_select_cv").html(text);
                        $("#civi_drop_cv").attr("data-attachment-id", "");
                        $("#candidate-profile-form")
                          .find(".point-mark")
                          .change();
                        icon_delete.remove();
                      },
                      error: function () {
                        icon_delete.html(
                          '<i class="far fa-trash-alt large"></i>'
                        );
                      },
                    });
                  });
                };
                civi_jobs_thumb_event("thumb");

                JOBS.elements.preview_job_tab();
              },
            });
          });
        }
      });
    },

    preview_job_tab: function () {
      $(".preview-tabs").each(function () {
        var _this = $(this),
          nav = _this.find("li a"),
          content = _this.find(".tab-content");

        nav.on("click", function (e) {
          e.preventDefault();
          var id = $(this).attr("href");

          nav.removeClass("is-active");
          content.removeClass("is-active");
          $(id).addClass("is-active");
          $(this).addClass("is-active");
          $("body").on("click", ".company-overview .content a", function (e) {
            e.preventDefault();
            $(this).parent().addClass("is-active");
            $(this).remove();
          });
          JOBS.elements.view_phone_number();
        });
      });
    },

    view_phone_number: function () {
      $(".company-phone").each(function () {
        var phone = $(this).find("a").attr("data-phone");
        var text = $(this).find("a").text();
        var el = $(this).find("a");
        var icon = $(this).find("i");
        var icon_view = "fa-eye";
        var icon_close = "fa-eye-slash";

        icon.on("click", function () {
          if (el.text() == text) {
            el.text(phone);
          } else {
            el.text(text);
          }
          if ($(this).hasClass(icon_view)) {
            $(this).removeClass(icon_view);
            $(this).addClass(icon_close);
          } else {
            $(this).removeClass(icon_close);
            $(this).addClass(icon_view);
          }
        });
      });
    },
  };

  JOBS.onReady = {
    init: function () {
      JOBS.element.init();
    },
  };

  JOBS.onLoad = {
    init: function () {},
  };

  JOBS.onScroll = {
    init: function () {
      JOBS.elements.waypoints();
    },
  };

  $(window).scroll(JOBS.onScroll.init);

  $(document).ready(function () {
    JOBS.elements.init();
  });

  $(window).load(JOBS.onLoad.init);
})(jQuery);
