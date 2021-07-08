var passwordFieled = document.querySelector('.password');
var show = document.querySelector('.show');
var hide = document.querySelector('.hide');

show.onclick = function(){
    passwordFieled.setAttribute("type", "text");
    show.style.display = "none";
    hide.style.display = "block";
}



