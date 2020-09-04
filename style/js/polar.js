"use strict";

function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
}

function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

function _iterableToArrayLimit(arr, i) {
    var _arr = [];
    var _n = true;
    var _d = false;
    var _e = undefined;
    try {
        for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
            _arr.push(_s.value);
            if (i && _arr.length === i) break;
        }
    } catch (err) {
        _d = true;
        _e = err;
    } finally {
        try {
            if (!_n && _i["return"] != null) _i["return"]();
        } finally {
            if (_d) throw _e;
        }
    }
    return _arr;
}

function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
}

function setOnlines(arr) {
    $('.block.monitoring').each(function(ii, el) {
        $(el).find('.server').each(function(i, el) {
            var _arr$i = _slicedToArray(arr[i], 2),
                cur = _arr$i[0],
                max = _arr$i[1];

            var percent = cur * 100 / max;
            var v = percent / 100 * 360;
            if (v >= 360) v = 359.999;
            $(el).find('.img svg path.val').attr('d', indicator(75, 75, 70, 0, v));
            $(el).find('.real-online span').text("".concat(cur, " \u0438\u0437 ").concat(max));
        });
    });
}

function polarToCartesian(cx, cy, radius, deg) {
    var rad = deg * Math.PI / 180;
    return {
        x: cx + radius * Math.cos(rad),
        y: cy + radius * Math.sin(rad)
    };
}

function indicator(x, y, radius, angleStart, angleEnd) {
    var start = polarToCartesian(x, y, radius, -angleEnd),
        end = polarToCartesian(x, y, radius, angleStart),
        largeArc = angleEnd - angleStart <= 180 ? 0 : 1;
    return ['M', start.x, start.y, 'A', radius, radius, 0, largeArc, 1, end.x, end.y].join(' ');
}