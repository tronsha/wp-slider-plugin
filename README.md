# WordPress Slider Plugin

## Documentation 

### Integrate 

The slider can be easily integrated with the shortcode `[slider /]`.

If you want to use the slider in the template so you can integrate it as follows. 

```php
<?php echo do_shortcode('[slider /]'); ?>
```

In this short form are all _.jpg_ and _.png_ images from the directory _wp-content/plugins/mpcx-slider/public/slides/_ used by the Slider.

It can also use the WordPress Gallery. 

```
[slider]
[gallery size="full" link="none" ids="5,6,7,8,9,10"]
[/slider]
```

Select for gallery `size="full"` or the desired image size.

### Attributes 

* __interval__: Waiting time to display the next slide. In milliseconds. Default: 10000
* __delay__: Time for changing. In milliseconds. Default: 1000
* __random__: Set _true_ to show slides random. Default: false
* __change__: Set _true_ to show the forward and back arrows. Default: false
* __position__: Set _true_ to show the position points. Default: false
* __text__: Show Text Box. Separate with __|__.

For example, the slider should be include into the header of the template. It should be every 5 seconds change a image.

```php
<?php echo do_shortcode('[slider interval="5000"]'); ?>
```

#### Font Awesome

Here an example, to use a [_Font Awesome_][5] symbol for the forward and back arrows.

```php
<?php echo do_shortcode('[slider change="fa" next="fa-caret-right" prev="fa-caret-left"]'); ?> 
```

## Require
* jQuery [Slider][3]
* [WordPress Requirements][4]

## Download

:package: [WordPress Plugins][6]

## Creator

**Stefan Hüsges**

:computer: [Homepage][1]

:octocat: [GitHub][2]

## License

[MIT License](LICENSE)

[1]: http://www.mpcx.net
[2]: https://github.com/tronsha
[3]: https://github.com/tronsha/slider
[4]: https://wordpress.org/about/requirements/
[5]: http://fortawesome.github.io/Font-Awesome/
[6]: https://wordpress.org/plugins/mpcx-slider/
