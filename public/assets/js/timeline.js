(function() {
			 
    'use strict';
  
    // define variables
    var timelines= document.querySelectorAll('.timeline2');
     
    function debounce(func, wait, immediate) {
      var timeout;
      return function() {
        var context = this, args = arguments;
        var later = function() {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    }
    function callbackFunc() {
        var h,timeline, li,rect,parent_rect,i,items;
      for(h=0;h<timelines.length;h++){
          timeline=timelines[h];
          parent_rect=timeline.getBoundingClientRect();
           items = timeline.querySelectorAll(".timeline2 li");
        for (  i = 0; i < items.length; i++) {
          /*
          if (isElementInViewport(items[i])) {
          items[i].classList.add("in-view");				   
          }
          */
          li=items[i];
          rect = li.getBoundingClientRect();  
           
          if( (rect.bottom<=(parent_rect.top+(rect.height/2) ) ) || (rect.top >=(parent_rect.bottom-(rect.height/2)) ) ){
            //debugger;
            //li.style['background']='red';
            li.classList.remove("in-view");
            
          }else{
            //li.style['background']='white';
            li.classList.add("in-view");
          }
           
        }
      }
    }
    var updateLayout =debounce(function(e) {
  
      // Does all the layout updating here
      callbackFunc();
      
    }, 500); // Maximum run of once per 500 milliseconds
  
    // listen for events
    window.addEventListener("load", callbackFunc);
    window.addEventListener("resize", updateLayout);
    window.addEventListener("scroll", callbackFunc);
    for(var h=0;h<timelines.length;h++){
        var  timeline=timelines[h];
      timeline.addEventListener("scroll",callbackFunc );
    }
    
   })();
    
  