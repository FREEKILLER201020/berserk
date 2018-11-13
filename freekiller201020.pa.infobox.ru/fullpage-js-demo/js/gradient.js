// *************************************
//
//  Original from Isuttell and
//  I modified some part for gradient effect
//  Forked Repo: https://github.com/cjies/sine-waves/
//
// *************************************

$(function() {

  var waves = new SineWaves({
    el: document.getElementById('waves'),
    speed: 0.6,
    width: function() {
      return $(window).width();
    },
    height: function() {
      return $(window).height();
    },
    wavesWidth: '95%',
    ease: 'SineInOut',
    // running: false,
    waves: [{
      timeModifier: 2,
      lineWidth: 1,
      amplitude: -50,
      wavelength: 200,
      segmentLength: 10,
      r: 255,
      // running: false,

      type: 'Sine',
      fillStyle: function(el, bound) {
        // console.log(el.color);
        var color = new RGBColor(el.style.color);
        // console.log(color);
        var gradient = this.ctx.createLinearGradient(bound.x0, bound.y0, bound.x1, bound.y1);
        gradient.addColorStop(0, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0.2)');
        gradient.addColorStop(0.5, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0)');
        return gradient;
      },
      strokeStyle: '',
      yAxis: function() {
        return this.height * 0.45;
      }
    }, {
      timeModifier: 1,
      lineWidth: 1,
      amplitude: 50,
      wavelength: 300,
      segmentLength: 10,
      type: 'Sine',
      fillStyle: function(el, bound) {
        var color = new RGBColor(el.style.color);
        var gradient = this.ctx.createLinearGradient(bound.x0, bound.y0, bound.x1, bound.y1);
        gradient.addColorStop(0, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0.2)');
        gradient.addColorStop(0.4, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0)');
        return gradient;
      },
      strokeStyle: '',
      yAxis: function() {
        return this.height * 0.55;
      }
    }, {
      timeModifier: 1,
      lineWidth: 1,
      amplitude: 80,
      wavelength: 500,
      segmentLength: 10,
      type: 'Sine',
      fillStyle: function(el, bound) {
        var color = new RGBColor(el.style.color);
        var gradient = this.ctx.createLinearGradient(bound.x0, bound.y0, bound.x1, bound.y1);
        gradient.addColorStop(0, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0.1)');
        gradient.addColorStop(0.3, 'rgba(' +
          color.r + ', ' +
          color.g + ', ' +
          color.b + ', 0)');
        return gradient;
      },
      strokeStyle: '',
    }],
    initialize: function() {},
    resizeEvent: function() {}
  });

});


/*!
 sine-waves v0.3.0-modified <https://github.com/isuttell/sine-waves>
 Contributor(s): Isaac Suttell <isaac@isaacsuttell.com>
 Last Build: 2015-11-03
*/
! function(a, b) {
  "use strict";
  "function" == typeof define && "object" == typeof define.amd ? define([], function() {
    return b(a)
  }) : a.SineWaves = b(a)
}(this, function() {
  "use strict";

  function a(a) {
    if (this.options = i.defaults(this.options, a), this.el = this.options.el, delete this.options.el, !this.el) throw "No Canvas Selected";
    this.ctx = this.el.getContext("2d"), this.waves = this.options.waves, delete this.options.waves, this.waves && i.isType(this.waves, "array") && this.waves.length || (this.waves = [], console.warn("No waves specified")), this.dpr = window.devicePixelRatio || 1, this.updateDimensions(), window.addEventListener("resize", this.updateDimensions.bind(this)), this.setupUserFunctions(), this.easeFn = i.getFn(n, this.options.ease, "linear"), this.rotation = i.degreesToRadians(this.options.rotate), i.isType(this.options.running, "boolean") && (this.running = true), this.setupWaveFns(), this.loop()
  }

  function b(a, b) {
    return i.isType(a, "number") ? a : (a = a.toString(), a.indexOf("%") > -1 ? (a = parseFloat(a), a > 1 && (a /= 100), b * a) : a.indexOf("px") > -1 ? parseInt(a, 10) : void 0)
  }
  Function.prototype.bind || (Function.prototype.bind = function(a) {
    if ("function" != typeof this) throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
    var b = Array.prototype.slice.call(arguments, 1),
      c = this,
      d = function() {},
      e = function() {
        return c.apply(this instanceof d && a ? this : a, b.concat(Array.prototype.slice.call(arguments)))
      };
    return d.prototype = this.prototype, e.prototype = new d, e
  });
  for (var c = ["ms", "moz", "webkit", "o"], d = 0; d < c.length && !window.requestAnimationFrame; ++d) window.requestAnimationFrame = window[c[d] + "RequestAnimationFrame"], window.cancelAnimationFrame = window[c[d] + "CancelAnimationFrame"] || window[c[d] + "CancelRequestAnimationFrame"];
  if (!window.requestAnimationFrame) {
    var e = 0;
    window.requestAnimationFrame = function(a) {
      var b = (new Date).getTime(),
        c = Math.max(0, 16 - (b - e)),
        d = window.setTimeout(function() {
          a(b + c)
        }, c);
      return e = b + c, d
    }
  }
  window.cancelAnimationFrame || (window.cancelAnimationFrame = function(a) {
    clearTimeout(a)
  });
  var f = Math.PI / 180,
    g = 2 * Math.PI,
    h = Math.PI / 2,
    i = {},
    j = i.isType = function(a, b) {
      var c = {}.toString.call(a).toLowerCase();
      return c === "[object " + b.toLowerCase() + "]"
    },
    k = i.isFunction = function(a) {
      return j(a, "function")
    },
    l = i.isString = function(a) {
      return j(a, "string")
    },
    m = (i.isNumber = function(a) {
      return j(a, "number")
    }, i.shallowClone = function(a) {
      var b = {};
      for (var c in a) a.hasOwnProperty(c) && (b[c] = a[c]);
      return b
    }),
    n = (i.defaults = function(a, b) {
      j(b, "object") || (b = {});
      var c = m(a);
      for (var d in b) b.hasOwnProperty(d) && (c[d] = b[d]);
      return c
    }, i.degreesToRadians = function(a) {
      if (!j(a, "number")) throw new TypeError("Degrees is not a number");
      return a * f
    }, i.getFn = function(a, b, c) {
      return k(b) ? b : l(b) && k(a[b.toLowerCase()]) ? a[b.toLowerCase()] : a[c]
    }, {});
  n.linear = function(a, b) {
    return b
  }, n.sinein = function(a, b) {
    return b * (Math.sin(a * Math.PI - h) + 1) * .5
  }, n.sineout = function(a, b) {
    return b * (Math.sin(a * Math.PI + h) + 1) * .5
  }, n.sineinout = function(a, b) {
    return b * (Math.sin(a * g - h) + 1) * .5
  };
  var o = {};
  return o.sine = function(a) {
    return Math.sin(a)
  }, o.sin = o.sine, o.sign = function(a) {
    return a = +a, 0 === a || isNaN(a) ? a : a > 0 ? 1 : -1
  }, o.square = function(a) {
    return o.sign(Math.sin(a * g))
  }, o.sawtooth = function(a) {
    return 2 * (a - Math.floor(a + .5))
  }, o.triangle = function(a) {
    return Math.abs(o.sawtooth(a))
  }, a.prototype.options = {
    speed: 10,
    accerate: 1,
    rotate: 0,
    ease: "Linear",
    wavesWidth: "95%"
  }, a.prototype.setupWaveFns = function() {
    for (var a = -1, b = this.waves.length; ++a < b;) this.waves[a].waveFn = i.getFn(o, this.waves[a].type, "sine")
  }, a.prototype.setupUserFunctions = function() {
    i.isFunction(this.options.resizeEvent) && (this.options.resizeEvent.call(this), window.addEventListener("resize", this.options.resizeEvent.bind(this))), i.isFunction(this.options.initialize) && this.options.initialize.call(this)
  }, a.prototype.getDimension = function(a) {
    return i.isNumber(this.options[a]) ? this.options[a] : i.isFunction(this.options[a]) ? this.options[a].call(this, this.el) : "width" === a ? this.el.clientWidth : "height" === a ? this.el.clientHeight : void 0
  }, a.prototype.getFillStyle = function(a, b) {
    return i.isString(a) ? a : i.isFunction(a) ? a.call(this, this.el, b) : void 0
  }, a.prototype.getYAxis = function(a) {
    return i.isNumber(a) ? a : i.isFunction(a) ? a.call(this, this.el) : void 0
  }, a.prototype.updateDimensions = function() {
    var a = this.getDimension("width"),
      c = this.getDimension("height");
    this.width = this.el.width = a * this.dpr, this.height = this.el.height = c * this.dpr, this.el.style.width = a + "px", this.el.style.height = c + "px", this.waveWidth = b(this.options.wavesWidth, this.width), this.waveLeft = (this.width - this.waveWidth) / 2, this.yAxis = this.height / 2
  }, a.prototype.clear = function() {
    this.ctx.clearRect(0, 0, this.width, this.height)
  }, a.prototype.time = 0, a.prototype.update = function(a) {
    this.time = this.time - .007, "undefined" == typeof a && (a = this.time);
    var b = -1,
      c = this.waves.length;
    for (this.clear(), this.ctx.save(), this.rotation > 0 && (this.ctx.translate(this.width / 2, this.height / 2), this.ctx.rotate(this.rotation), this.ctx.translate(-this.width / 2, -this.height / 2)); ++b < c;) {
      var d = this.waves[b].timeModifier || 1;
      this.drawWave(a * d, this.waves[b])
    }
    this.ctx.restore(), b = void 0, c = void 0
  }, a.prototype.getPoint = function(a, b, c) {
    var d = this.getYAxis(c.yAxis),
      e = this.options.speed * this.options.accerate,
      f = a * e + (-d + b) / c.wavelength,
      g = c.waveFn.call(this, f, o),
      h = this.easeFn.call(this, b / this.waveWidth, c.amplitude);
    return f = b + this.waveLeft, g = h * g + d, {
      x: f,
      y: g
    }
  }, a.prototype.drawWave = function(a, b) {
    var c = {
        timeModifier: 1,
        amplitude: 50,
        wavelength: 50,
        segmentLength: 10,
        lineWidth: 1,
        strokeStyle: "",
        fillStyle: "",
        type: "Sine",
        fillInverse: !1,
        yAxis: this.yAxis
      },
      d = {
        x0: 0,
        x1: 0,
        y0: this.height / 2,
        y1: this.height
      };
    b.fillInverse && (d = {
      x0: 0,
      x1: 0,
      y0: 0,
      y1: this.height / 2
    }), b = i.defaults(c, b), this.ctx.lineWidth = b.lineWidth * this.dpr, this.ctx.strokeStyle = b.strokeStyle, this.ctx.lineCap = "butt", this.ctx.lineJoin = "round", "" !== b.strokeStyle && (this.ctx.strokeStyle = b.strokeStyle), "" !== b.fillStyle && (this.ctx.fillStyle = this.getFillStyle(b.fillStyle, d));
    var e = this.getYAxis(b.yAxis);
    this.ctx.beginPath(), this.ctx.moveTo(0, e), this.ctx.lineTo(this.waveLeft, e);
    var f, g;
    for (f = 0; f < this.waveWidth; f += b.segmentLength) g = this.getPoint(a, f, b), this.ctx.lineTo(g.x, g.y), g = void 0;
    this.ctx.lineTo(this.width, e), "" !== b.fillStyle && (b.fillInverse ? (this.ctx.lineTo(this.width, 0), this.ctx.lineTo(0, 0)) : (this.ctx.lineTo(this.width, this.height), this.ctx.lineTo(0, this.height))), "" !== b.strokeStyle && this.ctx.stroke(), "" !== b.fillStyle && this.ctx.fill(), f = void 0, b = void 0, d = void 0
  }, a.prototype.running = !0, a.prototype.loop = function() {
    // console.log(this);
    //
    // var slider1 = document.getElementById("slider1");
    // var slider2 = document.getElementById("slider2");
    // var slider3 = document.getElementById("slider3");
    // var slider4 = document.getElementById("slider4");
    // var slider5 = document.getElementById("slider5");
    var obg = this;

    // function updateGradient2() {

    if ($ === undefined) return;

    var c0_0_g = colors_grad[colorIndices_grad[0]];
    var c0_1_g = colors_grad[colorIndices_grad[1]];

    var istep_g = 1 - step_g;
    var r1_g = Math.round(istep_g * c0_0_g[0] + step_g * c0_1_g[0]);
    var g1_g = Math.round(istep_g * c0_0_g[1] + step_g * c0_1_g[1]);
    var b1_g = Math.round(istep_g * c0_0_g[2] + step_g * c0_1_g[2]);
    var color1_g = "rgb(" + r1_g + "," + g1_g + "," + b1_g + ")";
    // console.log(step_g);

    // var r2 = Math.round(istep * c1_0[0] + step * c1_1[0]);
    // var g2 = Math.round(istep * c1_0[1] + step * c1_1[1]);
    // var b2 = Math.round(istep * c1_0[2] + step * c1_1[2]);
    // var color2 = "rgb("+r2+","+g2+","+b2+")";

    obg.el.style.color = color1_g;
    // $('#gradient').css({
    //   background: "-webkit-gradient(linear,  left top, left  bottom, from("+color1+"), to("+color2+"))"}).css({
    //    background: "-moz-linear-gradient(top, "+color1+" 0%, "+color2+" 100%)"});

    step_g += gradientSpeed;
    if (step_g >= 1) {
      step_g %= 1;
      colorIndices_grad[0] = colorIndices_grad[1];
      // colorIndices[2] = colorIndices[3];
      if (slider1.classList.contains('active')) {
        // colors[0]=colors[2];
        colorIndices_grad[1] = 1;
      }
      if (slider2.classList.contains('active')) {
        colorIndices_grad[1] = 2;
      }
      if (slider3.classList.contains('active')) {
        colorIndices_grad[1] = 3;
      }
      if (slider4.classList.contains('active')) {
        colorIndices_grad[1] = 4;
      }
      if (slider5.classList.contains('active')) {
        colorIndices_grad[1] = 5;
      }

      //pick two new target color indices
      //do not pick the same as the current one
      // colorIndices[1] = ( colorIndices[1] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
      // colorIndices[3] = ( colorIndices[3] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;

    }

    // function updateGradient()
    // {

    if ($ === undefined) return;

    var c0_0 = colors[colorIndices[0]];
    var c0_1 = colors[colorIndices[1]];
    var c1_0 = colors[colorIndices[2]];
    var c1_1 = colors[colorIndices[3]];

    var istep = 1 - step;
    var r1 = Math.round(istep * c0_0[0] + step * c0_1[0]);
    var g1 = Math.round(istep * c0_0[1] + step * c0_1[1]);
    var b1 = Math.round(istep * c0_0[2] + step * c0_1[2]);
    var color1 = "rgb(" + r1 + "," + g1 + "," + b1 + ")";

    var r2 = Math.round(istep * c1_0[0] + step * c1_1[0]);
    var g2 = Math.round(istep * c1_0[1] + step * c1_1[1]);
    var b2 = Math.round(istep * c1_0[2] + step * c1_1[2]);
    var color2 = "rgb(" + r2 + "," + g2 + "," + b2 + ")";

    $('#gradient').css({
      background: "-webkit-gradient(linear,  left top, left  bottom, from(" + color1 + "), to(" + color2 + "))"
    }).css({
      background: "-moz-linear-gradient(top, " + color1 + " 0%, " + color2 + " 100%)"
    });

    step += gradientSpeed;
    if (step >= 1) {
      step %= 1;
      colorIndices[0] = colorIndices[1];
      colorIndices[2] = colorIndices[3];
      if (slider1.classList.contains('active')) {
        // colors[0]=colors[2];
        colorIndices[1] = 2;
        colorIndices[3] = 3;
      }
      if (slider2.classList.contains('active')) {
        colorIndices[1] = 4;
        colorIndices[3] = 5;
      }
      if (slider3.classList.contains('active')) {
        colorIndices[1] = 6;
        colorIndices[3] = 7;
      }
      if (slider4.classList.contains('active')) {
        colorIndices[1] = 8;
        colorIndices[3] = 9;
      }
      if (slider5.classList.contains('active')) {
        colorIndices[1] = 10;
        colorIndices[3] = 11;
      }

      //pick two new target color indices
      //do not pick the same as the current one
      // colorIndices[1] = ( colorIndices[1] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
      // colorIndices[3] = ( colorIndices[3] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;

    }
    // }

    // setInterval(updateGradient,10);

    this.running === !0 && this.update(), window.requestAnimationFrame(this.loop.bind(this))
  }, a.prototype.Waves = o, a.prototype.Ease = n, a
});