// cookie policy
$(document).ready(function() {
    if (document.cookie.includes("accepted_cookies")) {
        $(".cookie-popup").addClass("hidden");
    }


    $("#cookie-close").on("click", function() {
        document.cookie = "accepted_cookies=yes;";
        $(".cookie-popup").addClass("hidden");
    });
});