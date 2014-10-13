# UPA Slider Plugin für WordPress

## Dokumentation

### Einbinden

Der Slider kann einfach mit dem ShortCode `[slider]` eingebunden werden.

Will man den Slider im Template verwenden so kann man ihn folgendermaßen einbinden. 

```php
<?php echo do_shortcode(["slider"]); ?> 
```

In dieser kurzen Form werden alle _jpg_ und _png_ Bilder aus dem Verzeichnis _wp-content/plugins/wp-slider-plugin/slider/images_ vom Slider verwendet.

Es lässt sich aber auch die WordPress Gallerie benutzen.

```
[slider]
[gallery size="full" link="none" ids="5,6,7,8,9,10"]
[/slider]
```

Bei der Gallerie darauf achten, dass `size="full"` bzw. die gewünschte Bildgröße gewählt ist.

### Attribute

* __delay__: Wechselzeit in Millisekunden. Standard: 1000
* __interval__: Anzeigedauer eines Bildes in Millisekunden. Standard: 10000
* __width__: Breite vom Slider. Standard: So breit wie möglich.
* __height__: Höhe vom Slider. Wird bei Verwendung der _responsive.js_ überschrieben.
* __text__: Textbox anzeigen. Texte für die Sliderbilder mit __|__ trennen.
* __change__: Auf _false_ setzen um die Vor- und Zurückpfeile auszublenden.
* __position__: Auf _false_ setzen um die Positionspunkte auszublenden.

## Lizenz

[MIT License](LICENSE)
