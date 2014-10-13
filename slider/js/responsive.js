window.onload = function () {
    sliderResize();
}

window.onresize = function () {
    sliderResize();
}

var sliderResize = function() {
    var $slider = jQuery('.slider');
    $slider.each(function() {
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
