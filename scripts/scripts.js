if (document.getElementById("commentform")){

document.getElementById("commentform").removeAttribute("novalidate");

}

(function () {
  function domReady (f) { /in/.test(document.readyState) ? setTimeout(domReady,16,f) : f() }

  function resize(event) {
    event.target.style.height = 'auto';
    event.target.style.height = event.target.scrollHeight+'px';
  }
  /* 0-timeout to get the already changed text */
  function delayedResize (event) {
    window.setTimeout(resize, 0, event);
  }

  domReady(function () {
    var textareas = document.querySelectorAll('textarea[auto-resize]')

    for (var i = 0, l = textareas.length; i < l; ++i) {
      var el = textareas.item(i)

      el.addEventListener('change',  resize, false);
      el.addEventListener('cut',     delayedResize, false);
      el.addEventListener('paste',   delayedResize, false);
      el.addEventListener('drop',    delayedResize, false);
      el.addEventListener('keydown', delayedResize, false);
    }
  })
}());

function moveCursorToEnd(el) {
    window.setTimeout(function () {
            if (typeof el.selectionStart == "number") {
            el.selectionStart = el.selectionEnd = el.value.length;
        } else if (typeof el.createTextRange != "undefined") {
            var range = el.createTextRange();
            range.collapse(false);
            range.select();
        }
    }, 1);
}


function lh_comments_createcss(content) {
	// Create the <style> tag
	var style = document.createElement("style");

	// Add a media (and/or media query) here if you'd like!
        style.setAttribute("media", "screen")


	// WebKit hack :(
	style.appendChild(document.createTextNode(content));

	// Add the <style> element to the page
	document.head.appendChild(style);

	return style.sheet;
};

if (document.getElementById("lh_comments-fieldset")){

lh_comments_createcss(".lh_comments-inactive { display: none; }");

document.getElementById("lh_comments-fieldset").setAttribute("class", "lh_comments-inactive");

}


function lh_comments_return_canonical() {
                var canonical = "";
                var links = document.getElementsByTagName("link");
                for (var i = 0; i < links.length; i ++) {
                    if (links[i].getAttribute("rel") === "canonical") {
                        canonical = links[i].getAttribute("href")
                    }
                }
return canonical;
            };




document.getElementById("comment").onfocus=function(){

document.getElementById("lh_comments-fieldset").setAttribute("class", "lh_comments-active");
};

if (document.getElementById("comment")){

var canonical = lh_comments_return_canonical();
var field = "comment";

if (localStorage.getItem("lh_comments-data")){

var data = JSON.parse(localStorage.getItem("lh_comments-data"));


} else {

var data = {}; // {} will create an object

}

document.getElementById("comment").addEventListener("keyup", function(evt) {

var add = {}; // {} will create an object

add[field]  = evt.target.value.trim();

data[canonical]  = add;

var dataToStore = JSON.stringify(data);

localStorage.setItem('lh_comments-data', dataToStore);


    }, false);


if (document.getElementById("comment").value == ""){

if (data[canonical][field].replace(/\s/g,"") != ""){

document.getElementById("comment").value = data[canonical][field];



if (document.location.hash === "#respond"){




setTimeout(function(){
document.getElementById('comment').focus();
moveCursorToEnd(document.getElementById('comment'));
document.getElementById("comment").style.height = 'auto';
document.getElementById("comment").style.height = document.getElementById("comment").scrollHeight+'px';
},1);



} else {

document.getElementById("comment").style.height = 'auto';
document.getElementById("comment").style.height = document.getElementById("comment").scrollHeight+'px';


}


}



}



document.getElementById("commentform").addEventListener("submit", function(){

var add = {}; // {} will create an object

add[field]  = "";

data[canonical]  = add;

var dataToStore = JSON.stringify(data);

localStorage.setItem('lh_comments-data', dataToStore);
});



}


