(function(e){function t(t){e("#ajax-response").removeClass("collect-folders").html(t).delay(1e4).hide(2e3,"linear",function(){e(this).html("")})}function n(r){data={action:"eazyest_gallery_collect_folders",subaction:r,_wpnonce:eazyestGalleryCollect._wpnonce};e.post(ajaxurl,data,function(e){if("next"==e)n(e);else t(e)})}e(document).ready(function(){if(pagenow==eazyestGalleryCollect.pagenow){e("#ajax-response").html(eazyestGalleryCollect.collecting).addClass("collect-folders").show("fast",function(){n("start")})}})})(jQuery)