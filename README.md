# WordPress Slider Plugin

## Documentation 

### Integrate 

The slider can be easily integrated with the shortcode `[slider]`. 

If you want to use the slider in the template so you can integrate it as follows. 

```php
<?php echo do_shortcode('[slider]'); ?> 
```

In this short form are all _.jpg_ and _.png_ images from the directory _wp-content/plugins/wp-slider-plugin/slider/slides/_ used by the Slider. 

It can also use the WordPress Gallery. 

```
[slider]
[gallery size="full" link="none" ids="5,6,7,8,9,10"]
[/slider]
```

Select for gallery `size="full"` or the desired image size.

### Attributes 

* __delay__: Time for changing. In milliseconds. Default: 1000
* __interval__: Waiting time to display the next slide. In milliseconds. Default: 10000 
* __width__: Width of the slider. Default: As wide as possible. 
* __height__: Height of the slider. Will be overwritten using the responsive.js. 
* __text__: Show Text Box. Separate the texts for the slides with __|__. 
* __change__: Set to __false__ to hide the forward and back arrow.
* __position__: Set to __false__ to hide the position points.

For example, the slider should be include into the header of the template. It should be every 5 seconds change a image and be 640 pixels wide and 400 pixels high.

```php
<?php echo do_shortcode('[slider interval="5000" width="640" height="400"]'); ?> 
```

#### Font Awesome

Here an example, to use a [_Font Awesome_][4] symbol for the next and prev button.

```php
<?php echo do_shortcode('[slider change="fa" next="fa-caret-right" prev="fa-caret-left"]'); ?> 
```

## Require
* jQuery [Slider][3]
* [PHP][5] 5.3.2 or newer

## Creator

**Stefan Hüsges**

* [GitHub][1]
* [Homepage][2]

## License

[MIT License](LICENSE)

[1]: https://github.com/tronsha
[2]: http://www.mpcx.net
[3]: https://github.com/tronsha/slider
[4]: http://fortawesome.github.io/Font-Awesome/
[5]: http://php.net
