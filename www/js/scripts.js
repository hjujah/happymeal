/*
 Various Authors
 * */
 
var styles = [
{
    featureType: 'water',
    elementType: 'all',
    stylers: [
        { hue: '#f8f6ea' },
        { saturation: 9 },
        { lightness: 77 },
        { visibility: 'on' }
    ]
},{
    featureType: 'landscape',
    elementType: 'all',
    stylers: [
        { hue: '#e2cfaf' },
        { saturation: 27 },
        { lightness: -12 },
        { visibility: 'on' }
    ]
},{
    featureType: 'water',
    elementType: 'labels',
    stylers: [
        { hue: '#3d3b46' },
        { saturation: -81 },
        { lightness: -67 },
        { visibility: 'on' }
    ]
},{
    featureType: 'road',
    elementType: 'all',
    stylers: [
        { hue: '#c3a66a' },
        { saturation: -57 },
        { lightness: -8 },
        { visibility: 'on' }
    ]
},{
    featureType: 'transit',
    elementType: 'all',
    stylers: [
        { hue: '#d7be94' },
        { saturation: 46 },
        { lightness: -5 },
        { visibility: 'on' }
    ]
},{
    featureType: 'road.arterial',
    elementType: 'geometry',
    stylers: [
        { hue: '#ffffff' },
        { saturation: -100 },
        { lightness: 100 },
        { visibility: 'on' }
    ]
},{
    featureType: 'poi',
    elementType: 'all',
    stylers: [
        { hue: '#d7be94' },
        { saturation: 46 },
        { lightness: -5 },
        { visibility: 'on' }
    ]
},{
    featureType: 'road.local',
    elementType: 'geometry',
    stylers: [
        { hue: '#ffffff' },
        { saturation: -100 },
        { lightness: 100 },
        { visibility: 'on' }
    ]
},
{
    featureType: "poi",
    elementType: "labels",
    stylers: [
          { visibility: "off" }
    ]
}
];

function isIE() { 
    return ((navigator.appName == 'Microsoft Internet Explorer') || ((navigator.appName == 'Netscape') && (new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})").exec(navigator.userAgent) != null))); 
}

// check if Internet Explorer 11
var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);

(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());

var isiPad = navigator.userAgent.match(/iPad/i) != null;

if (navigator.userAgent.match(/iPad;.*CPU.*OS 7_\d/i)) {
	$('html').addClass('ipad ios7');
}

define(['jquery'], function($) {

    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    
    
     
    if (!Object.keys) {
	  Object.keys = function(obj) {
	    var keys = [];
	
	    for (var i in obj) {
	      if (obj.hasOwnProperty(i)) {
	        keys.push(i);
	      }
	    }
	
	    return keys;
	  };
	}
	
    
    // make it safe to use console.log always
	(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
	(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());
    
    $.fn.getRotationDegrees = function(){
    		var obj = this;
    		var matrix = obj.css("-webkit-transform") ||
			obj.css("-moz-transform")    ||
			obj.css("-ms-transform")     ||
			obj.css("-o-transform")      ||
			obj.css("transform");
			if(matrix !== 'none') {
			    var values = matrix.split('(')[1].split(')')[0].split(',');
			    var a = values[0];
			    var b = values[1];
			    var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
			} else { var angle = 0; }
			return (angle < 0) ? angle +=360 : angle;
    }

    $.fn.reverse = function() {
		return $(this.get().reverse());
	}
	

	
    $.fn.getDocHeight = function() {
	    var D = this[0];
        var height = Math.max(
		    Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
		    Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
		    Math.max(D.body.clientHeight, D.documentElement.clientHeight)
		);
		//console.log("he",height);
		//console.log("isi",navigator.userAgent.match(/iPad/i) != null);
		return (navigator.userAgent.match(/iPad/i) != null) ? height  : height;
		return height;
    }
	
	
	
	Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	    c = isNaN(c = Math.abs(c)) ? 2 : c, 
	    d = d == undefined ? "." : d, 
	    t = t == undefined ? "." : t, 
	    s = n < 0 ? "-" : "", 
	    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	    j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };

    $.fn.isOnScreen = function() {
        var win = $(window);
        var viewport = {
            top: win.scrollTop(),
            left: win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();
        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth() ;
        bounds.bottom = bounds.top + this.outerHeight();
        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
    };
    

    /**
     * jQuery.browser.mobile (http://detectmobilebrowser.com/)
     *
     * jQuery.browser.mobile will be true if the browser is a mobile device
     *
     **/ (function(a) {
        (jQuery.browser = jQuery.browser || {}).mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))
    })(navigator.userAgent || navigator.vendor || window.opera);



    // make it safe to use console.log always
	(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
	(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());

    function getScrollTop() {
        if (typeof pageYOffset != 'undefined') {
            //most browsers
            return pageYOffset;
        } else {
            var B = document.body; //IE 'quirks'
            var D = document.documentElement; //IE with doctype
            D = (D.clientHeight) ? D : B;
            return D.scrollTop;
        }
    }

    //Chunk array
    Array.prototype.chunk = function(chunkSize) {
        var array = this;
        return [].concat.apply([],
        array.map(function(elem, i) {
            return i % chunkSize ? [] : [array.slice(i, i + chunkSize)];
        }));
    }
    
    
     //get last array member
    Array.prototype.last = function() {
    	return this[this.length-1]
    }

    //Swap elements in array
    Array.prototype.swapItems = function(a, b) {
        this[a] = this.splice(b, 1, this[a])[0];
        return this;
    }

    //Clone array
    Array.prototype.clone = function() {
        return this.slice(0);
    };


    /**
     * Request Animation Frame Polyfill.
     * @author Tino Zijdel
     * @author Paul Irish
     * @see https://gist.github.com/paulirish/1579671
     */;
    (function() {

        var lastTime = 0;
        var vendors = ['ms', 'moz', 'webkit', 'o'];

        for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
            window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
            window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
        }

        if (!window.requestAnimationFrame) {
            window.requestAnimationFrame = function(callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function() {
                    callback(currTime + timeToCall);
                },
                timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };
        }

        if (!window.cancelAnimationFrame) {
            window.cancelAnimationFrame = function(id) {
                clearTimeout(id);
            };
        }

    }());
    
    //shuffle dome elements
     $.fn.shuffle = function() {
 
        var allElems = this.get(),
            getRandom = function(max) {
                return Math.floor(Math.random() * max);
            },
            shuffled = $.map(allElems, function(){
                var random = getRandom(allElems.length),
                    randEl = $(allElems[random]).clone(true)[0];
                allElems.splice(random, 1);
                return randEl;
           });
 
        this.each(function(i){
            $(this).replaceWith($(shuffled[i]));
        });
 
        return $(shuffled);
 
    };
    
    
    /*
	 * Konami Code For jQuery Plugin
	 * 1.2.1, 23 October 2013
	 *
	 * Using the Konami code, easily configure and Easter Egg for your page or any element on the page.
	 *
	 * Copyright 2011 - 2013 Tom McFarlin, http://tommcfarlin.com
	 * Released under the MIT License
	 */
	(function(e) {
	    "use strict";
	    e.fn.konami = function(t) {
	        var n, r, i, s;
	        n = e.extend({}, e.fn.konami.defaults, t);
	        return this.each(function() {
	            i = [];
	            e(window).keyup(function(e) {
	                s = e.keyCode || e.which;
	                if (n.code.length > i.push(s)) {
	                    return
	                }
	                if (n.code.length < i.length) {
	                    i.shift()
	                }
	                if (n.code.toString() !== i.toString()) {
	                    return
	                }
	                n.cheat()
	            })
	        })
	    };
	    e.fn.konami.defaults = {
	        code: [38, 38, 40, 40, 37, 39, 37, 39, 66, 65],
	        cheat: null
	    }
	})(jQuery)
    
    //capitalize first letter
    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }

    /*Scroll Positions plugin*/! function(a) {
        a.fn.scrollEvents = function(b) {
            var c = {
                start: 1,
                end: 99,
                factor: 1,
                method: "width",
                measures: "percentage",
                ending: !1
            }, b = a.extend(c, b);
            b.start, b.end;
            var g = this,
                h = a(window),
                i = h.innerHeight(),
                j = a(this).scrollTop(),
                k = h.height(),
                l = a(".about").height();
            i = l - l / 6, b.ending && (b.end = b.end - k), "percentage" == b.measures && (b.start = i / 100 * b.start, b.end = i / 100 * b.end);
            var m = (b.end - b.start) / 100,
                n = function() {
                    j = a(this).scrollTop(), scrollStart = (j - b.start) / m, j >= b.start && j <= b.end ? desetice = scrollStart / 100 : j > b.start ? desetice = 1 : j < b.end && (desetice = 0), desetice = 100 * b.factor * desetice, margina = 100 - desetice, "width" == b.method && g.css("width", margina + "%")
                };
            h.scroll(n)
        }
    }(jQuery);

}); 