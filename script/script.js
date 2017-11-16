var checkBoxes;
var hashTags;
var searchField;

var init = function() {
    hashTags = document.querySelectorAll("#hashtags button");
    searchField = document.querySelector("#searchfield");
    checkBoxes = document.querySelectorAll('.checkbox');

    for (var hashTag of hashTags) {
        hashTag.addEventListener("click", pushToSearch);
    }
    for (var checkBox of checkBoxes) {
        checkBox.addEventListener("click", checked);
    }
    
}
window.onload = init;


function pushToSearch() {
    this.classList.toggle('btn-warning');
    this.classList.toggle('btn-success');
    this.classList.toggle('active');
    if (searchField.value != "" && this.classList.contains('active')) {
        searchField.value += " " + this.innerHTML;
    } else if (this.classList.contains('active')) {
        searchField.value += this.innerHTML;
    } else {
        if (searchField.value.length > this.innerHTML.length ) {
            searchField.value = searchField.value.replace(" "+this.innerHTML,"");

        } else {
            searchField.value = searchField.value.replace(this.innerHTML,"");
        }
    }
    
} 

function checked() {
    this.classList.toggle('btn-warning');
    this.classList.toggle('btn-success');
    this.classList.toggle('active');
    console.log(this.firstElementChild);
    console.log("hej");
    if (this.firstElementChild.hasAttribute('checked')){
        this.firstElementChild.removeAttribute('checked');
    } else {
    this.firstElementChild.setAttribute('checked', "");
    }
} 