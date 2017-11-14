
var  hashtags;
var searchfield;

var init = function() {
    hashtags = Document.querySelectorAll("#hastags button");
    searchfield = Document.querySelector("#searchfield");
    for (var hashtag of hastags) {
        hashtag.addEventListener("click", pushToSearch);
    }
}
window.onload = init;


function pushToSearch() {
    console.log("körs detta");
    searchfield.value = this.value;
} 