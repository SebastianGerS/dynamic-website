
var  hashtags;
var searchfield;

var init = function() {
    hashtags = document.querySelectorAll("#hastags  button");
    searchfield = document.querySelector("#searchfield");
    for (var hashtag of hastags) {
        hashtag.addEventListener("click", pushToSearch);
    }
}
window.onload = init;


function pushToSearch() {
    console.log("körs detta");
    searchfield.value = this.value;
} 