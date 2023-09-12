// Fonction qui permet d'éclaircir/assombrir une couleur
function LightenDarkenColor(col, amt) {
  var num = parseInt(col, 16);
  var r = (num >> 16) + amt;
  var b = ((num >> 8) & 0x00FF) + amt;
  var g = (num & 0x0000FF) + amt;
  var newColor = g | (b << 8) | (r << 16);
  return newColor.toString(16);
}






function hexToRgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result
    ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
      ]
    : [0, 0, 0];
}


function rgbToHex(r,g,b)
{
  return '#' + this.byteToHex(r) + this.byteToHex(g) + this.byteToHex(b);
}
function byteToHex (n)
{
  var nybHexString = "0123456789ABCDEF";
  return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
}

function hslToHex(h,s,l)
{
  let a=s*Math.min(l,1-l);
  let f= (n,k=(n+h/30)%12) => l - a*Math.max(Math.min(k-3,9-k,1),-1);
  return rgbToHex(f(0),f(8),f(4));
}

// Détermine si la couleur du texte doit être claire ou sombre en fonction de la couleur de fond
function getDarkLightTextColorByBackgroundColor(hex) {
  var rgb = hexToRgb(hex)
  var lrgb = [];
  rgb.forEach(function(c) {
    c = c / 255.0;
    if (c <= 0.03928) {
      c = c / 12.92;
    } else {
      c = Math.pow((c + 0.055) / 1.055, 2.4);
    }
    lrgb.push(c);
  });
  var lum = 0.2126 * lrgb[0] + 0.7152 * lrgb[1] + 0.0722 * lrgb[2];
  return lum > 0.179 ? "dark" : "light";
}
