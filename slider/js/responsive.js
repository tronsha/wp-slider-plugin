window.onload = function () {
    sliderResize();
}

window.onresize = function () {
    sliderResize();
}

var sliderResize = function () {
    var $slider = jQuery('.slider');
    $slider.each(function () {
        var $this = jQuery(this);
        $this.css('max-width', '100%');
        var $activeImage = $this.find('.slides .active');
        if ($activeImage.length > 0) {
            var imageWidth = $activeImage[0].naturalWidth;
            var imageHeight = $activeImage[0].naturalHeight;
            var sliderWidth = $this.width();
            var sliderHeight = sliderWidth * imageHeight / imageWidth;
            $this.height(sliderHeight);
        }
    });
}

var sliderResizeTextBox = function {
    if (window.matchMedia('(max-width: 767px)').matches) {
        var height = parseInt(jQuery('.slider .text span.active').css('height'));
        jQuery('.slider .text span').css('bottom', -height);
        jQuery('.slider .text span').css('left', 0);
        jQuery('.slider .text span').css('right', 0);
        jQuery('.slider').css('margin-bottom', height);
    } else {
        jQuery('.slider .text span').css('bottom', '');
        jQuery('.slider .text span').css('left', '');
        jQuery('.slider .text span').css('right', '');
        jQuery('.slider').css('margin-bottom', '');
    }
}
