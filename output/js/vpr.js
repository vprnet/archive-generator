/**
 * Created by Colin Urban on 5/2/14.
 */
$(function () {
    $("img.lazy").lazyload();
    $(".cat").click(function () {
        var cat = this.innerHTML;
        var stories = $(".top_story");
        stories.each(function () {
            $(this).show();
        });
        if (cat !== "View All") {
            stories.each(function () {
                var c = $(this).find('.category')[0].innerHTML;
                console.log(c);
                console.log(cat);
                if (c !== cat) {
                    $(this).hide();
                }
            });
        }
    });
});