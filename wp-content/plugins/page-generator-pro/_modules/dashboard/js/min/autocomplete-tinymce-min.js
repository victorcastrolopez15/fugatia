!function(){const e=40,t=38,n=27,i=13,o=8;var l=[e,t,n,i],s=["123"];"undefined"!=typeof wpzinc_autocomplete&&wpzinc_autocomplete.forEach((function(o,c){o.triggers.forEach((function(o,c){"url"in o||(tinymce.create("tinymce.plugins."+o.tinyMCEName,{init:function(c){function r(){var e=document.createElement("ul");return e.setAttribute("class","wpzinc-tinymce-autocomplete"),o.values.forEach((function(t,n){var i=document.createElement("li");i.classList.add("displayed"),e.appendChild(i),i.innerHTML=i.innerHTML+t.value})),document.body.appendChild(e),e}function a(e,t){var n=d(e);g(t,n.top,n.left),t.classList.remove("displayed"),t.classList.add("displayed"),k=!0}function d(e){var t=e.getContainer()?e.getContainer():document.getElementById(e.id),n=t.getBoundingClientRect().top+window.scrollY,i=t.getBoundingClientRect().left+window.scrollX,o={top:0,left:0};o=e.selection.getRng().getClientRects().length>0?{top:e.selection.getRng().getClientRects()[0].top+20,left:e.selection.getRng().getClientRects()[0].left}:{top:e.selection.getNode().getClientRects()[0].top+20,left:e.selection.getNode().getClientRects()[0].left};var l=t.getElementsByClassName("mce-toolbar-grp")[0];return l?{top:n+l.getBoundingClientRect().height+o.top,left:i+o.left}:o}function g(e,t,n){e.style.marginTop=t+"px",e.style.marginLeft=n+"px"}function u(e){var t=null==e.selection.getSel().focusNode?"":e.selection.getSel().focusNode.nodeValue,n=e.selection.getSel().focusOffset,i=0,o;if(null==t||0==t.length)return"";for(var l=n;l>=0;l--)if(-1!=s.indexOf(t.charCodeAt(l).toString())){i=l;break}return{search:t.substr(i,n-i),start:i,end:n}}function f(e,t,n){for(var i=n.getElementsByTagName("li"),o=!0,l=i.length,s=0;s<l-1;s++)i.item(s).classList.remove("highlight"),-1==i.item(s).innerText.indexOf(e.search)?i.item(s).classList.remove("displayed"):(i.item(s).classList.add("displayed"),o&&(i.item(s).classList.add("highlight"),o=!1))}function m(e,t,n){for(var i=n.querySelectorAll("li.displayed"),o=i.length,l=0;l<o-1;l++)if(i[l].classList.contains("highlight")){if("previous"==e){if(0==l)break;i[l].classList.remove("highlight"),i[l-1].classList.add("highlight");break}if("next"==e){if(l==i.length-1)break;i[l].classList.remove("highlight"),i[l+1].classList.add("highlight");break}}}function h(e,t){var n;p(t.querySelectorAll("li.highlight")[0].innerText,e,t)}function p(e,t){var n=u(t),i=t.selection.getSel().focusNode,o=t.selection.getRng();o.setStart(i,n.start),o.setEnd(i,n.end),t.selection.setRng(o),t.selection.setContent(e)}function y(e,t){t.classList.remove("displayed"),k=!1}function v(l,s){if(o.triggerKeyCode==s.keyCode&&!k){if(!o.triggerKeyShiftRequired)return void a(l,E);if(s.shiftKey)return void a(l,E)}if(n!=s.keyCode||!k){var c;if(k&&(t==s.keyCode||e==s.keyCode))m(t==s.keyCode?"previous":"next",l,E);return k&&i==s.keyCode?(tinymce.dom.Event.cancel(s),h(l,E),void y(l,E)):void 0}y(l,E)}function C(e,t){var n;-1==l.indexOf(t.keyCode)&&k&&(a(e,E),f(u(e),e,E))}function w(e,t){y(e,E)}function L(e){e.target.matches("li.displayed")?(p(e.target.innerText,c),y(c,E)):y(c,E)}var k=!1,E=r();c.onKeyDown.add(C),c.onKeyDown.add(v),c.onClick.add(w),document.addEventListener("click",L)},getInfo:function(){return{longname:"Autocomplete",author:"WP Zinc",authorurl:"https://www.wpzinc.com/",infourl:"https://www.wpzinc.com/",version:tinymce.majorVersion+"."+tinymce.minorVersion}}}),tinymce.PluginManager.add(o.tinyMCEName,tinymce.plugins[o.tinyMCEName]))}))}))}();