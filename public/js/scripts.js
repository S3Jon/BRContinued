document.addEventListener("DOMContentLoaded", function() {
    const dropdownButton = document.getElementById("dropdown-button");
    const dropdownMenu = document.getElementById("dropdown");
 
    dropdownButton.addEventListener("click", function() {
        dropdownMenu.classList.toggle("hidden");
    });
});