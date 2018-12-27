function openTab(evt, filterKey) {

  // Get all elements with class="tabContent" and hide them
  var tabContent = document.getElementsByClassName("tabContent");
  for (var i = 0; i < tabContent.length; i++) {
    tabContent[i].style.display = "none";
  }

  // Get all elements with class="tab" and remove the class "active"
  var tab = document.getElementsByClassName("tabs");
  for (var i = 0; i < tab.length; i++) {
    tab[i].className = tab[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(filterKey).style.display = "block";
  evt.currentTarget.className += " active";
}