# UPA Slider Plugin für WordPress

## Dokumentation

_in Arbeit_

Der Slider kann einfach mit dem ShortCode `[slider]` eingebunden werden.

Will man den Slider im Template verwenden so kann man ihn folgendermaßen einbinden. 

```php
<?php echo do_shortcode(["slider"]); ?> 
```

In dieser kurzen Form werden alle _jpg_ und _png_ Bilder aus dem Order _wp-content/plugins/wp-slider-plugin/slider/images_ vom Slider verwendet.

Es lässt sich aber auch die WordPress Gallerie benutzen.

```
[slider]
[gallery size="full" link="file" ids="5,6,7,8,9,10"]
[/slider]
```

Bei der Gallerie darauf achten, dass `size="full"` ist.

## Lizenz

[MIT License](LICENSE)
