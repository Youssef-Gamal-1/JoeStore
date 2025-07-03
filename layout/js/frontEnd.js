// slider

const initSlider = () =>{
    let btns = document.querySelectorAll(".slider i");
    let imageList = document.querySelector(".slider .image-list");
    let maxScrollWidth = imageList.scrollWidth - imageList.clientWidth;
    let scrollbar = document.querySelector(".scrollbar");
    let scrollbarThumb = document.querySelector(".scrollbar-thumb");

    

    scrollbarThumb.addEventListener("mousedown",(e) => {
        let startX = e.clientX;
        let thumbPosition = scrollbarThumb.offsetLeft;

        let handleMouseMove = (e) =>{
            let deltaX = e.clientX - startX;
            let newThumbPosition = thumbPosition + deltaX;
            let maxThumbPosition = scrollbar.getBoundingClientRect().width - scrollbarThumb.offsetWidth;
            let boundedPosition = Math.max(0,Math.min(maxThumbPosition,newThumbPosition));

            let scrollPosition = (boundedPosition / maxThumbPosition) * maxScrollWidth;

            scrollbarThumb.style.left = `${boundedPosition}px`;
            imageList.scrollLeft = scrollPosition;
        }
        
        let handleMouseUp = () =>{
            document.removeEventListener("mousemove",handleMouseMove);
            document.removeEventListener("mouseup",handleMouseUp);
        }
        
        document.addEventListener("mousemove",handleMouseMove);
        document.addEventListener("mouseup",handleMouseUp);


    })

    // slide images according to buttons clicks

    btns.forEach((button) =>{
        button.addEventListener("click",() =>{
            direction = button.id === "prev" ? -1 : 1;
            let scrollAmount = imageList.clientWidth * direction;
            imageList.scrollBy({left: scrollAmount, behavior: "smooth"});
        })
    })

    let handleSlideBtns = function(){
        btns[0].style.display = imageList.scrollLeft <= 0 ? "none" : "block";
        btns[1].style.display = imageList.scrollRight >= maxScrollWidth ? "none" : "block";
    }

    
    let updateScrollThumbPoisiton = function(){
        let scrollPosition = imageList.scrollLeft;
        let thumbPosition = (scrollPosition / maxScrollWidth) 
                                * (scrollbar.clientWidth - scrollbarThumb.offsetWidth);
        scrollbarThumb.style.left = `${thumbPosition}px` ;
    }
    
    imageList.addEventListener("scroll",() => {
        handleSlideBtns();
        updateScrollThumbPoisiton();
    });
}

window.addEventListener("load",initSlider);



let cardNum = document.querySelector("#credit-num");
let btn = document.querySelector("input[type='submit']#purchase");
let cardValue = '';

cardNum.addEventListener("input",function(e){
    cardLength = cardNum.value.length;
    if(cardLength === 4 || cardLength === 9 || cardLength === 14){
        cardNum.value = `${cardNum.value} `;

        cardValue = cardNum.value.replaceAll(" ","");
        if(cardValue != '' && isNaN(Number(cardValue))){
            cardNum.style.color = "red";
        }
    }
    
    if(cardLength === 19){
        this.setAttribute("onkeydown","return false");
    }

});

btn.addEventListener("click",function(e){
    // let regex = /(\d{4}\s){3}\d{4}/g;
    // 
    if(!regex.test(cardNum.value) && isNaN(Number(cardValue))){
        e.preventDefault();
        let created = document.createElement("div");
        created.innerHTML = "Enter a valid value please";
        created.style.color = "red";
        document.body.appendChild(created);
    }
});