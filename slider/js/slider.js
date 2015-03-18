;
(
	function ( $, window, document, undefined ) {

		"use strict";

		var pluginName = "slider";
		var defaults = {
			delay: 1000,
			interval: 10000
		};

		function Plugin( element, options ) {
			this.element = element;
			this.settings = $.extend( {}, defaults, options );
			this.vari = {
				timer: undefined,
				slide: 1,
				slides: 0
			};
			this._defaults = defaults;
			this._name = pluginName;
			this.init();
		}

		Plugin.prototype = {
			init: function () {
				var self = this;
				var $slider = $( this.element );
				var $position = $slider.find( '.position' );
				$slider.children( '.slides' ).find( 'img' ).each( function ( index, element ) {
					self.vari.slides ++;
					if ( self.vari.slides == self.vari.slide ) {
						$( element ).addClass( 'active' ).css( 'opacity', '1' );
						$position.append( '<div class="points active"></div>' );
					} else {
						$( element ).css( 'opacity', '0' );
						$position.append( '<div class="points"></div>' );
					}
				} );
				$position.find( '.points' ).each( function ( index ) {
					$( this ).click( function () {
						self.show( index + 1 );
					} );
				} );
				$slider.hover( function () {
					clearInterval( self.vari.timer );
				}, function () {
					self.auto();
				} );
				$slider.find( '.prev > *' ).click( function () {
					self.prev();
				} );
				$slider.find( '.next > *' ).click( function () {
					self.next();
				} );
				this.auto();
			},
			auto: function () {
				var self = this;
				this.vari.timer = setInterval( function () {
					self.next();
				}, this.settings.interval );
			},
			next: function () {
				if ( this.vari.slide < this.vari.slides ) {
					this.show( this.vari.slide + 1 );
				} else {
					this.show( 1 );
				}
			},
			prev: function () {
				if ( this.vari.slide > 1 ) {
					this.show( this.vari.slide - 1 );
				} else {
					this.show( this.vari.slides );
				}
			},
			show: function ( slide ) {
				$( this.element ).find( '.slides img:nth-child(' + this.vari.slide + ')' ).stop().removeClass( 'active' ).animate( {opacity: 0}, this.settings.delay );
				$( this.element ).find( '.position .points:nth-child(' + this.vari.slide + ')' ).removeClass( 'active' );
				$( this.element ).find( '.text span:nth-child(' + this.vari.slide + ')' ).removeClass( 'active' );
				this.vari.slide = slide;
				$( this.element ).find( '.slides img:nth-child(' + this.vari.slide + ')' ).stop().addClass( 'active' ).animate( {opacity: 1}, this.settings.delay );
				$( this.element ).find( '.position .points:nth-child(' + this.vari.slide + ')' ).addClass( 'active' );
				$( this.element ).find( '.text span:nth-child(' + this.vari.slide + ')' ).addClass( 'active' );
				return this;
			}
		};

		$.fn[pluginName] = function ( options ) {
			this.each( function () {
				if ( ! $.data( this, "plugin_" + pluginName ) ) {
					$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
				}
			} );
			return this;
		};

	}
)( jQuery, window, document );
