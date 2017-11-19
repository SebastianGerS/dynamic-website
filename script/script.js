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

window.onload = init; //makes sure that all content is loaded before script is initialized


function pushToSearch() {

    this.classList.toggle('btn-warning');
    this.classList.toggle('btn-success');
    this.classList.toggle('active');

    if (searchField.value != "" && this.classList.contains('active')) {

        searchField.value += " " + this.innerHTML;

    } else if (this.classList.contains('active')) {

        searchField.value = this.innerHTML;

    } else if (searchField.value.length > this.innerHTML.length ) {

        searchField.value = searchField.value.replace(this.innerHTML,"");
        searchField.value = searchField.value.replace(" ","");

    } else {

        searchField.value = searchField.value.replace(this.innerHTML,"");
    }
    
} /* this function is conected to several eventlisteners and will toggel classes on or off on
     the eventlistener is contected to. It will also push the objects value to a input 
     field if (the value) it's not in the field and remove it if it's there*/

function checked() {

    this.classList.toggle('btn-warning');
    this.classList.toggle('btn-success');
    this.classList.toggle('active');

    if (this.firstElementChild.hasAttribute('checked')){

        this.firstElementChild.removeAttribute('checked');

    } else {

        this.firstElementChild.setAttribute('checked', "");
    }
} //this function is conected to several eventlisteners and will toggels classes and atributes on or off when triggerd